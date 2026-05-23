<?php

namespace App\Http\Controllers;

use App\Entities\Patient;
use App\Entities\FacialEvaluation;
use App\Services\FacialEvaluationService;
use App\Services\PatientService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Repositories\FacialEvaluationRepository;
use App\Validators\FacialEvaluationValidator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Prettus\Validator\Contracts\ValidatorInterface;
use Carbon\Carbon;

/**
 * Class FacialEvaluationsController.
 *
 * @package namespace App\Http\Controllers;
 */
class FacialEvaluationsController extends Controller
{
    /**
     * @var FacialEvaluationRepository
     */
    protected $service;

    /**
     * @var FacialEvaluationValidator
     */
    protected $validator;

    /**
     * @param FacialEvaluationService $service
     * @param FacialEvaluationValidator $validator
     * @param PatientService $patientService
     */
    public function __construct(FacialEvaluationService         $service,
                                FacialEvaluationValidator       $validator,
                                private readonly PatientService $patientService)
    {
        $this->service   = $service;
        $this->validator = $validator;
    }


    /**
     * Abre o index.blade com as fichas do paciente.
     *
     * @param int $patientId
     * @return View|RedirectResponse
     */
    public function panelIndex(int $patientId)
    {
        $patient = $this->patientService->find($patientId, true);

        if (!$patient) {
            return redirect()->route('panel.patient.index')->with('error', 'Paciente não encontrado.');
        }

        return view('facial-evaluations.index',[
            'title'    => 'Avaliação facial do paciente '.$patient->social_name,
            'subtitle' => 'Painel de Controle',
            'patient' => $patient
        ]);
    }

    /**
     * Abre o formulario para criar uma nova ficha facial.
     *
     * @param int $patientId
     * @return View|RedirectResponse
     */
    public function create(int $patientId,Request $request)
    {
        $patient = $this->patientService->find($patientId, true);

        if (!$patient) {
            return redirect()->route('panel.patient.index')->with('error', 'Paciente não encontrado.');
        }
        $professional_id = $request->attributes->get('user_jwt')->id ?? 1;
        return view('facial-evaluations.form', [
            'title'    => 'Cadastro de avaliação facial',
            'subtitle' => 'Painel de Controle',
            'patient'  => $patient,
            'professional_id'  => $professional_id,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $payload = $this->normalizePayload($request->all());
            $this->validator->with($payload)->passesOrFail(ValidatorInterface::RULE_CREATE);
            return response()->json($this->service->create($payload));
        } catch (\Exception $exception) {
            return $this->sendBadResponse($exception);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $payload = $this->normalizePayload($request->all());
            $this->validator->with($payload)->passesOrFail(ValidatorInterface::RULE_UPDATE);
            return response()->json($this->service->update($payload, $id));
        } catch (\Exception $exception) {
            return $this->sendBadResponse($exception);
        }
    }

    /**
     * Abre o formulario para editar uma ficha facial.
     *
     * @param int $id
     * @return View|RedirectResponse
     */
    public function edit(int $id, Request $request)
    {
        $facialEvaluation = $this->service->find($id, true);

        if (!$facialEvaluation) {
            return redirect()->route('panel.patient.index')->with('error', 'Ficha de avaliação não encontrada.');
        }

        $patient = $this->patientService->find($facialEvaluation->patient_id, true);

        if (!$patient) {
            return redirect()->route('panel.patient.index')->with('error', 'Paciente da ficha não encontrado.');
        }
        $professional_id = $request->attributes->get('user_jwt')->id ?? 1;
        return view('facial-evaluations.edit', [
            'title' => 'Editar avaliacao facial',
            'subtitle' => 'Painel de Controle',
            'patient' => $patient,
            'facialEvaluation' => $facialEvaluation,
            'professional_id'  => $professional_id
        ]);
    }

    public function panelShow(int $id,Request $request): View
    {
        $facialEvaluation = $this->service->find($id, true);

        if (!$facialEvaluation) {
            return redirect()->route('panel.patient.index')->with('error', 'Ficha de avaliação não encontrada.');
        }
        $professional_id = $request->attributes->get('user_jwt')->id ?? 1;
        return view('facial-evaluations.show', [
            'title' => 'Visualizar avaliacao facial',
            'subtitle' => 'Painel de Controle',
            'facialEvaluation' => $facialEvaluation,
            'professional_id'  => $professional_id,
        ]);
    }


     /**
      * Generate a signature token for the patient
      *
      * @param int $id
      * @return JsonResponse
      */
     public function generateSignatureToken(int $id)
     {
         try {
             $facialEvaluation = $this->service->find($id, true);

             if (!$facialEvaluation) {
                 return response()->json([
                     'error' => true,
                     'message' => 'Ficha de avaliação não encontrada.'
                 ], 404);
             }

             $token     = Str::random(64);
             $expiresAt = Carbon::now()->addDays(7);

             $facialEvaluation->signature_token = $token;
             $facialEvaluation->signature_token_expires_at = $expiresAt;
             $facialEvaluation->save();

             $signatureUrl = route('facial-evaluation.sign', ['token' => $token]);

             return response()->json([
                 'error'         => false,
                 'message'       => 'Token gerado com sucesso.',
                 'token'         => $token,
                 'signature_url' => $signatureUrl,
                 'expires_at'    => $expiresAt->format('d/m/Y H:i:s'),
             ]);
         } catch (\Exception $e) {
             return response()->json([
                 'error'   => true,
                 'message' => 'Erro ao gerar token: ' . $e->getMessage()
             ], 422);
         }
     }

     /**
      * Show public signature form
      *
      * @param string $token
      * @return View
      */
     public function showSignatureForm(string $token)
     {
         $facialEvaluation = $this->service->findWhere(['signature_token'=> $token],true);

         if (!$facialEvaluation || !$facialEvaluation->isTokenValid()) {
             return view('facial-evaluations.signature-expired');
         }

         return view('facial-evaluations.sign', compact('facialEvaluation', 'token'));
     }

     /**
      * Process patient signature
      *
      * @param Request $request
      * @param string $token
      * @return JsonResponse
      */
     public function processSignature(Request $request, string $token)
     {
         try {
             $facialEvaluation = $this->service->findWhere(['signature_token'=> $token],true);

             if (!$facialEvaluation) {
                 return response()->json([
                     'error'   => true,
                     'message' => 'Token inválido.'
                 ], 404);
             }

             if (!$facialEvaluation->isTokenValid()) {
                 return response()->json([
                     'error'   => true,
                     'message' => 'Token expirado.'
                 ], 410);
             }

             $request->validate([
                 'patient_signature' => 'required|string',
                 'consent_accepted'  => 'required|boolean',
             ]);


             $facialEvaluation->patient_signature = $request->patient_signature;
             $facialEvaluation->consent_accepted = $request->consent_accepted;
             $facialEvaluation->signed_at = Carbon::now();
             $facialEvaluation->signature_token = null;
             $facialEvaluation->signature_token_expires_at = null;
             $facialEvaluation->save();

             return response()->json([
                 'error'   => false,
                 'message' => 'Assinatura registrada com sucesso!',
             ]);
         } catch (\Exception $e) {
             return response()->json([
                 'error'   => true,
                 'message' => 'Erro ao processar assinatura: ' . $e->getMessage()
             ], 422);
         }
     }

     /**
      * Send signature link via WhatsApp
      *
      * @param int $id
      * @return JsonResponse
      */
     public function sendSignatureLink(int $id)
     {
         try {
             $facialEvaluation = $this->service->find($id, true);

             if (!$facialEvaluation) {
                 return response()->json([
                     'error' => true,
                     'message' => 'Ficha de avaliação não encontrada.'
                 ], 404);
             }

             if (!$facialEvaluation->signature_token || !$facialEvaluation->isTokenValid()) {
                 return response()->json([
                     'error' => true,
                     'message' => 'Token inválido ou expirado. Gere um novo token.'
                 ], 422);
             }

             $patient = $facialEvaluation->getChatAttributes();
             $phone   = $patient['chat_id'];
             $name    = $patient['social_name'];



             $signatureUrl = route('facial-evaluation.sign', ['token' => $facialEvaluation->signature_token]);

             $message = "Olá, $name!\n\n" .
                       "Sua ficha de avaliação facial foi concluída. " .
                       "Por favor, confirme e assine através do link abaixo:\n\n" .
                       "{$signatureUrl}\n\n" .
                       "Este link expira em 7 dias.";
             $this->service->sendMessageToWhatsApp($phone, $message);

             return response()->json([
                 'error'         => false,
                 'message'       => 'Link preparado para envio.',
                 'whatsapp_url'  => "",
                 'signature_url' => $signatureUrl,
             ]);
         } catch (\Exception $e) {
             return response()->json([
                 'error'   => true,
                 'message' => 'Erro ao preparar link: ' . $e->getMessage()
             ], 422);
         }

     }

     private function normalizePayload(array $payload): array
     {
         $booleanFields = [
             'acne',
             'melasma',
             'wrinkles',
             'flaccidity',
             'spots',
             'dilated_pores',
             'consent_accepted',
         ];

         foreach ($booleanFields as $field) {
             $payload[$field] = filter_var($payload[$field] ?? false, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
         }

         foreach (['oiliness', 'hydration', 'sensitivity', 'patient_id', 'professional_id'] as $field) {
             if (array_key_exists($field, $payload) && $payload[$field] !== null && $payload[$field] !== '') {
                 $payload[$field] = (int) $payload[$field];
             }
         }

         if (isset($payload['treatment_plan']) && is_array($payload['treatment_plan'])) {
             $sessions = $payload['treatment_plan']['sessions'] ?? null;
             if ($sessions !== null && $sessions !== '') {
                 $payload['treatment_plan']['sessions'] = (int) $sessions;
             }
         }

         return $payload;
     }
}
