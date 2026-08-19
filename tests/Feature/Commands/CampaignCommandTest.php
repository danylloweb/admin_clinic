<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_requires_campaign_id()
    {
        $this->artisan('campaign:run')
            ->expectsOutput('Opção --campaignId é obrigatória')
            ->assertExitCode(1);
    }

    public function test_command_fails_with_invalid_campaign()
    {
        $this->artisan('campaign:run', ['--campaignId' => 9999])
            ->expectsOutput('Campanha 9999 não encontrada')
            ->assertExitCode(1);
    }

    public function test_command_succeeds_with_no_patients()
    {
        // Verificar se as classes de modelo existem antes de testar
        if (!class_exists('App\Models\Campaign')) {
            $this->markTestSkipped('Campaign model not found');
        }

        // Criar uma campanha sem pacientes
        $campaign = \App\Models\Campaign::factory()->create();

        $this->artisan('campaign:run', ['--campaignId' => $campaign->id])
            ->expectsOutput('Nenhum paciente encontrado para campanha')
            ->assertExitCode(0);
    }

    public function test_command_displays_scheduling_info()
    {
        // Verificar se as classes de modelo existem antes de testar
        if (!class_exists('App\Models\Campaign') || !class_exists('App\Models\Patient')) {
            $this->markTestSkipped('Campaign or Patient model not found');
        }

        // Criar campanha e pacientes
        $campaign = \App\Models\Campaign::factory()->create([
            'description' => 'Olá {name}!',
            'url_image' => 'https://example.com/image.jpg',
        ]);

        \App\Models\Patient::factory()->count(5)->create([
            'chat_id' => '5511999999999',
        ]);

        $this->artisan('campaign:run', ['--campaignId' => $campaign->id])
            ->expectsOutput('📊 Agendando 5 mensagens')
            ->expectsOutputToContain('jobs agendadas com sucesso')
            ->assertExitCode(0);
    }
}

