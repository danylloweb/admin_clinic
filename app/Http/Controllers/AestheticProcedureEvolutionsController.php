<?php

namespace App\Http\Controllers;

use App\Entities\Schedule;
use App\Services\AestheticProcedureEvolutionService;
use App\Services\PatientService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Prettus\Validator\Contracts\ValidatorInterface;
use App\Validators\AestheticProcedureEvolutionValidator;
class AestheticProcedureEvolutionsController extends Controller
{
    protected $service;

    protected $validator;

    public function __construct(
        AestheticProcedureEvolutionService $service,
        AestheticProcedureEvolutionValidator $validator,
        private readonly PatientService $patientService
    )
    {
        $this->service = $service;
        $this->validator = $validator;
    }

    public function panelIndex(int $patientId)
    {
        $patient = $this->patientService->find($patientId, true);

        if (!$patient) {
            return redirect()->route('panel.patient.index')->with('error', 'Paciente nao encontrado.');
        }

        return view('aesthetic-procedure-evolutions.index', [
            'title' => 'Atendimento do paciente ' . $patient->social_name,
            'subtitle' => 'Painel de Controle',
            'patient' => $patient,
        ]);
    }

    public function openBySchedule(int $scheduleId): RedirectResponse
    {
        $schedule = Schedule::query()->find($scheduleId);
        if (!$schedule) {
            return redirect()->route('panel.schedules.index')->with('error', 'Agendamento nao encontrado.');
        }

        $attendance = $this->service->findWhere(['schedule_id' => $scheduleId], true);
        if ($attendance) {
            return redirect()->route('panel.attendances.edit', ['id' => $attendance->id]);
        }

        return redirect()->route('panel.attendances.create', [
            'patientId' => $schedule->patient_id,
            'schedule_id' => $scheduleId,
        ]);
    }

    public function create(int $patientId, Request $request)
    {
        $patient = $this->patientService->find($patientId, true);
        if (!$patient) {
            return redirect()->route('panel.patient.index')->with('error', 'Paciente nao encontrado.');
        }

        $scheduleId = (int) $request->query('schedule_id', 0);
        $schedule = $scheduleId ? Schedule::query()->find($scheduleId) : null;

        return view('aesthetic-procedure-evolutions.form', [
            'title' => 'Cadastro de atendimento',
            'subtitle' => 'Painel de Controle',
            'patient' => $patient,
            'attendance' => null,
            'schedule' => $schedule,
            'professional_id' => $request->attributes->get('user_jwt')->id ?? 1,
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

    public function edit(int $id, Request $request)
    {
        $attendance = $this->service->find($id, true);
        if (!$attendance) {
            return redirect()->route('panel.schedules.index')->with('error', 'Atendimento nao encontrado.');
        }

        $patient = $this->patientService->find($attendance->patient_id, true);
        if (!$patient) {
            return redirect()->route('panel.patient.index')->with('error', 'Paciente nao encontrado.');
        }

        $schedule = $attendance->schedule_id ? Schedule::query()->find($attendance->schedule_id) : null;

        return view('aesthetic-procedure-evolutions.form', [
            'title' => 'Editar atendimento',
            'subtitle' => 'Painel de Controle',
            'patient' => $patient,
            'attendance' => $attendance,
            'schedule' => $schedule,
            'professional_id' => $request->attributes->get('user_jwt')->id ?? 1,
        ]);
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

    public function panelShow(int $id)
    {
        $attendance = $this->service->find($id, true);
        if (!$attendance) {
            return redirect()->route('panel.schedules.index')->with('error', 'Atendimento nao encontrado.');
        }

        $patient = $this->patientService->find($attendance->patient_id, true);

        return view('aesthetic-procedure-evolutions.show', [
            'title' => 'Visualizar atendimento',
            'subtitle' => 'Painel de Controle',
            'attendance' => $attendance,
            'patient' => $patient,
        ]);
    }

    public function print(int $id)
    {
        $attendance = $this->service->find($id, true);
        if (!$attendance) {
            return redirect()->route('panel.schedules.index')->with('error', 'Atendimento nao encontrado.');
        }

        $patient = $this->patientService->find($attendance->patient_id, true);

        return view('aesthetic-procedure-evolutions.pdf', [
            'attendance' => $attendance,
            'patient' => $patient,
            'isPrint' => true,
        ]);
    }

    public function exportPdf(int $id)
    {
        $attendance = $this->service->find($id, true);
        if (!$attendance) {
            return redirect()->route('panel.schedules.index')->with('error', 'Atendimento nao encontrado.');
        }

        $patient = $this->patientService->find($attendance->patient_id, true);
        $html = view('aesthetic-procedure-evolutions.pdf', [
            'attendance' => $attendance,
            'patient' => $patient,
            'isPrint' => false,
        ])->render();

        return response()->streamDownload(function () use ($html) {
            echo Pdf::loadHTML($html)->setPaper('a4')->output();
        }, 'atendimento-' . $id . '.pdf', ['Content-Type' => 'application/pdf']);
    }

    private function normalizePayload(array $payload): array
    {
        foreach (['schedule_id', 'patient_id', 'professional_id'] as $field) {
            if (array_key_exists($field, $payload) && $payload[$field] !== null && $payload[$field] !== '') {
                $payload[$field] = (int) $payload[$field];
            }
        }

        foreach (['evolution_sessions'] as $field) {
            if (!array_key_exists($field, $payload) || $payload[$field] === null || $payload[$field] === '') {
                continue;
            }

            if (is_string($payload[$field])) {
                $decoded = json_decode($payload[$field], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $payload[$field] = $decoded;
                }
            }
        }

        return $payload;
    }
}
