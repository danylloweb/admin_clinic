<?php

namespace App\Http\Controllers;

use App\Entities\Patient;
use App\Entities\BodyEvaluation;
use App\Services\BodyEvaluationService;
use App\Services\PatientService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Repositories\BodyEvaluationRepository;
use App\Validators\BodyEvaluationValidator;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * Class BodyEvaluationsController.
 *
 * @package namespace App\Http\Controllers;
 */
class BodyEvaluationsController extends Controller
{
    /**
     * @var BodyEvaluationService
     */
    protected $service;

    /**
     * @var BodyEvaluationValidator
     */
    protected $validator;

    /**
     * @param BodyEvaluationService $service
     * @param BodyEvaluationValidator $validator
     * @param PatientService $patientService
     */
    public function __construct(BodyEvaluationService   $service,
                                BodyEvaluationValidator $validator,
                                private PatientService    $patientService)
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

        return view('body-evaluations.index',[
            'title'    => 'Avaliação corporal do paciente '.$patient->social_name,
            'subtitle' => 'Painel de Controle',
            'patient'  => $patient
        ]);
    }

    /**
     * Abre o formulario para criar uma nova ficha corporal.
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

        return view('body-evaluations.form', [
            'title' => 'Cadastro de avaliação corporal',
            'subtitle' => 'Painel de Controle',
            'patient' => $patient,
            'bodyEvaluation' => null,
        ]);
    }

    /**
     * Armazena uma nova ficha corporal.
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */


    /**
     * Abre o formulario para editar uma ficha corporal.
     *
     * @param int $id
     * @return View|RedirectResponse
     */
    public function edit(int $id)
    {
        $bodyEvaluation = $this->service->find($id, true);

        if (!$bodyEvaluation) {
            return redirect()->route('panel.patient.index')->with('error', 'Ficha de avaliação não encontrada.');
        }

        $patient = $this->patientService->find($bodyEvaluation->patient_id, true);

        if (!$patient) {
            return redirect()->route('panel.patient.index')->with('error', 'Paciente da ficha não encontrado.');
        }

        return view('body-evaluations.show', compact('patient', 'bodyEvaluation'));
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
            $bodyEvaluation = $this->service->find($id, true);

            if (!$bodyEvaluation) {
                return response()->json([
                    'error' => true,
                    'message' => 'Ficha de avaliação não encontrada.'
                ], 404);
            }

            $token     = Str::random(64);
            $expiresAt = Carbon::now()->addDays(7);

            $bodyEvaluation->signature_token = $token;
            $bodyEvaluation->signature_token_expires_at = $expiresAt;
            $bodyEvaluation->save();

            $signatureUrl = route('body-evaluation.sign', ['token' => $token]);

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
        $bodyEvaluation = $this->service->findWhere(['signature_token'=> $token],true);

        if (!$bodyEvaluation || !$bodyEvaluation->isTokenValid()) {
            return view('body-evaluations.signature-expired');
        }

        return view('body-evaluations.sign', compact('bodyEvaluation', 'token'));
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
            $bodyEvaluation = $this->service->findWhere(['signature_token'=> $token],true);

            if (!$bodyEvaluation) {
                return response()->json([
                    'error'   => true,
                    'message' => 'Token inválido.'
                ], 404);
            }

            if (!$bodyEvaluation->isTokenValid()) {
                return response()->json([
                    'error'   => true,
                    'message' => 'Token expirado.'
                ], 410);
            }

            $request->validate([
                'patient_signature' => 'required|string',
                'consent_accepted'  => 'required|boolean',
            ]);

            $bodyEvaluation->patient_signature = $request->patient_signature;
            $bodyEvaluation->consent_accepted = $request->consent_accepted;
            $bodyEvaluation->signed_at = Carbon::now();
            $bodyEvaluation->signature_token = null;
            $bodyEvaluation->signature_token_expires_at = null;
            $bodyEvaluation->save();

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
            $bodyEvaluation = $this->service->find($id, true);

            if (!$bodyEvaluation) {
                return response()->json([
                    'error' => true,
                    'message' => 'Ficha de avaliação não encontrada.'
                ], 404);
            }

            if (!$bodyEvaluation->signature_token || !$bodyEvaluation->isTokenValid()) {
                return response()->json([
                    'error' => true,
                    'message' => 'Token inválido ou expirado. Gere um novo token.'
                ], 422);
            }

            $patient = $bodyEvaluation->getChatAttributes();
            $phone   = $patient['chat_id'];
            $name    = $patient['social_name'];

            $signatureUrl = route('body-evaluation.sign', ['token' => $bodyEvaluation->signature_token]);

            $message = "Olá, $name!\n\n" .
                       "Sua ficha de avaliação corporal foi concluída. " .
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


