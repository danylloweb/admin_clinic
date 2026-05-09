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
    public function __construct(FacialEvaluationService   $service,
                                FacialEvaluationValidator $validator,
                                private PatientService    $patientService)
    {
        $this->service   = $service;
        $this->validator = $validator;
    }

    /**
     * Abre a pagina index.blade de avaliacoes faciais.
     *
     * @param int $patientId
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */


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
    public function create(int $patientId)
    {
        $patient = $this->patientService->find($patientId, true);

        if (!$patient) {
            return redirect()->route('panel.patient.index')->with('error', 'Paciente não encontrado.');
        }

        return view('facial-evaluations.form', [
            'title' => 'Cadastro de avaliação facial',
            'subtitle' => 'Painel de Controle',
            'patient' => $patient,
        ]);
    }

    /**
     * Abre o formulario para editar uma ficha facial.
     *
     * @param int $id
     * @return View|RedirectResponse
     */
    public function edit(int $id)
    {
        $facialEvaluation = $this->service->find($id, true);

        if (!$facialEvaluation) {
            return redirect()->route('panel.patient.index')->with('error', 'Ficha de avaliação não encontrada.');
        }

        $patient = $this->patientService->find($facialEvaluation->patient_id, true);

        if (!$patient) {
            return redirect()->route('panel.patient.index')->with('error', 'Paciente da ficha não encontrado.');
        }

        return view('facial-evaluations.edit', compact('patient', 'facialEvaluation'));
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
}
