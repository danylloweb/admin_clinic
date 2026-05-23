<?php

namespace Tests\Feature;

use App\Entities\AestheticProcedureEvolution;
use App\Entities\Schedule;
use App\Http\Middleware\VerifyCsrfToken;
use App\Models\User;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AttendanceE2ETest extends TestCase
{
    public function test_attendance_end_to_end_flow(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $user = User::query()->first();
        if (!$user) {
            $this->markTestSkipped('No user found for JWT authentication.');
        }

        $schedule = Schedule::query()
            ->whereNotIn('id', function ($query) {
                $query->select('schedule_id')
                    ->from('aesthetic_procedure_evolutions')
                    ->whereNotNull('schedule_id');
            })
            ->orderBy('id')
            ->first();

        if (!$schedule) {
            $this->markTestSkipped('No schedule available without attendance relation.');
        }

        $token = JWTAuth::fromUser($user);
        $authHeaders = [
            'Authorization' => 'Bearer ' . $token,
            'Cookie' => 'jwt_token=' . $token,
            'Accept' => 'application/json',
        ];

        $createdId = null;

        try {
            $openBySchedule = $this->withHeaders($authHeaders)
                ->get('/panel-attendances-open-by-schedule/' . $schedule->id);
            $openBySchedule->assertStatus(302);

            $createPage = $this->withHeaders($authHeaders)
                ->get('/panel-attendances-create/' . $schedule->patient_id . '?schedule_id=' . $schedule->id);
            $createPage->assertStatus(200);

            $payload = [
                'schedule_id' => $schedule->id,
                'patient_id' => $schedule->patient_id,
                'professional_id' => $schedule->professional_id ?: $user->id,
                'procedure_name' => 'Teste E2E Atendimento',
                'start_date' => '2026-05-23',
                'evolution_sessions' => [
                    [
                        'session_number' => 1,
                        'date' => '2026-05-23',
                        'time' => '09:30',
                        'procedure_performed' => 'Limpeza de pele',
                        'equipment_used' => 'Ultrassom',
                        'parameters_used' => 'Intensidade 2',
                        'products_used' => 'Gel calmante',
                        'patient_reaction' => 'Boa',
                        'observations' => 'Sem intercorrencias',
                    ],
                ],
                'result_evaluation' => 'Bom',
                'signed_at' => '2026-05-23 09:45:00',
            ];

            $create = $this->withHeaders($authHeaders)
                ->postJson('/panel-attendances-store', $payload);
            $create->assertStatus(200);

            $createdId = $create->json('data.id') ?: $create->json('id');
            $this->assertNotEmpty($createdId, 'Expected created attendance id in response payload.');

            $update = $this->withHeaders($authHeaders)
                ->putJson('/panel-attendances-update/' . $createdId, [
                    'result_evaluation' => 'Excelente',
                ]);
            $update->assertStatus(200);

            $show = $this->withHeaders($authHeaders)
                ->get('/panel-attendances-show/' . $createdId);
            $show->assertStatus(200);
            $show->assertSee('Visualizar atendimento');

            $print = $this->withHeaders($authHeaders)
                ->get('/panel-attendances-print/' . $createdId);
            $print->assertStatus(200);
            $print->assertSee('Atendimento');

            $pdf = $this->withHeaders($authHeaders)
                ->get('/panel-attendances-export-pdf/' . $createdId);
            $pdf->assertStatus(200);
            $pdf->assertHeader('content-type', 'application/pdf');

        } finally {
            if ($createdId) {
                AestheticProcedureEvolution::withTrashed()->where('id', $createdId)->forceDelete();
            }
        }
    }
}

