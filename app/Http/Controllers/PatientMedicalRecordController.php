<?php

namespace App\Http\Controllers;

use App\Entities\Patient;
use App\Entities\PatientMedicalRecord;
use App\Http\Requests\PatientMedicalRecordCreateRequest;
use App\Services\PatientService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PatientMedicalRecordController extends Controller
{
    public function __construct(private PatientService $patientService)
    {
    }

    public function successStatus(): View|Factory|Application
    {
        return view('patient_medical_records.status', [
            'title' => 'Prontuário enviado',
            'headline' => 'Recebemos seu prontuário com sucesso.',
            'description' => 'Obrigado! Nossa equipe da Renovar vai analisar as informações antes do seu atendimento.',
            'tone' => 'success',
        ]);
    }

    public function issueLink(int $patientId): JsonResponse
    {
        $patient = Patient::query()->findOrFail($patientId);
        $record  = PatientMedicalRecord::query()->firstOrNew(['patient_id' => $patient->id]);

        if (!$record->exists || !$record->hasPendingToken()) {
            $record->fill([
                'patient_id'         => $patient->id,
                'access_token'       => $this->generateUniqueToken(),
                'token_generated_at' => now(),
                'submitted_at'       => null,
            ]);
            $record->save();
        }

        $link = $record->publicUrl();
        $whatsappSent = false;
        $whatsappError = null;

        if (!empty($link)) {
            try {
                $chatId      = $patient->chat_id ?: $this->patientService->getContactIdByPhone((string) $patient->phone);
                $patientName = $patient->social_name ?: $patient->name;
                $message     = "Ola {$patientName}, tudo bem? para continuarmos seu atendimento na Renovar, preencha seu prontuario pelo link: {$link}";
                $this->patientService->sendMessageToWhatsApp($chatId, $message);
                $whatsappSent = true;
            } catch (\Throwable $exception) {
                $whatsappError = 'Nao foi possivel enviar a mensagem no WhatsApp automaticamente.';
                Log::warning('PatientMedicalRecordController issueLink whatsapp error: '.$exception->getMessage());
            }
        }

        return response()->json([
            'patient_id'         => $patient->id,
            'patient_name'       => $patient->social_name ?: $patient->name,
            'status'             => $record->isSubmitted() ? 'preenchido' : 'pendente',
            'link'               => $link,
            'token_generated_at' => optional($record->token_generated_at)->toDateTimeString(),
            'whatsapp_sent'      => $whatsappSent,
            'whatsapp_error'     => $whatsappError,
        ]);
    }

    public function panelShow(int $patientId): View|Factory|Application
    {
        $patient = Patient::query()->findOrFail($patientId);
        $record = PatientMedicalRecord::query()->where('patient_id', $patientId)->first();

        return view('patient_medical_records.show', [
            'title' => 'Prontuario do Paciente',
            'subtitle' => 'Visualizacao completa do prontuario',
            'patient' => $patient,
            'record' => $record,
            'routeCreate' => route('panel.patient.show', ['id' => $patientId]),
        ]);
    }

    public function publicForm(string $token): View|Factory|Application
    {
        $record = PatientMedicalRecord::query()
            ->with('patient')
            ->where('access_token', $token)
            ->first();

        if (!$record || !$record->patient) {
            return view('patient_medical_records.status', [
                'title' => 'Link inválido',
                'headline' => 'Este link não está mais disponível.',
                'description' => 'Peça para nossa equipe gerar um novo link do prontuário pelo WhatsApp.',
                'tone' => 'warning',
            ]);
        }

        return view('patient_medical_records.form', [
            'record' => $record,
            'patient' => $record->patient,
            'title' => 'Prontuário do Paciente',
        ]);
    }

    public function submitPublicForm(PatientMedicalRecordCreateRequest $request, string $token)
    {
        $record = PatientMedicalRecord::query()
            ->with('patient')
            ->where('access_token', $token)
            ->first();

        if (!$record || !$record->patient) {
            return response()->json([
                'message' => 'Este link nao esta mais disponivel. Solicite um novo link para nossa equipe.',
            ], 404);
        }

        $data = $request->validated();

        $record->fill($data);
        $record->lgpd_consent = (bool) ($data['lgpd_consent'] ?? false);
        $record->token_generated_at = null;
        $record->save();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Recebemos seu prontuario com sucesso.',
                'status' => 'success',
                'redirect_to' => route('patient-medical-record.success'),
            ]);
        }

        return view('patient_medical_records.status', [
            'title' => 'Prontuário enviado',
            'headline' => 'Recebemos seu prontuário com sucesso.',
            'description' => 'Obrigado! Nossa equipe da Renovar vai analisar as informações antes do seu atendimento.',
            'tone' => 'success',
        ]);
    }

    private function generateUniqueToken(): string
    {
        do {
            $token = Str::random(64);
        } while (PatientMedicalRecord::query()->where('access_token', $token)->exists());

        return $token;
    }

    public function formSuccess(Request $request, string $token)
    {
        $record = PatientMedicalRecord::query()->where('access_token', $token)->first();
        if (!$record) {
            return view('patient_medical_records.status', [
                'title'       => 'Link inválido',
                'headline'    => 'Este link não está mais disponível.',
                'description' => 'Peça para nossa equipe gerar um novo link do prontuário pelo WhatsApp.',
                'tone'        => 'warning',
            ]);
        }
        $record->token_generated_at = null;
        $record->access_token = null;
        $record->save();

        return view('patient_medical_records.status', [
            'title' => 'Prontuário enviado',
            'headline' => 'Recebemos seu prontuário com sucesso.',
            'description' => 'Obrigado! Nossa equipe da Renovar vai analisar as informações antes do seu atendimento.',
            'tone' => 'success',
        ]);
    }

}

