<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('body_evaluations', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('patient_id')->index();
            $table->unsignedInteger('professional_id')->nullable()->index();

            // Medidas antropométricas
            $table->float('weight')->nullable();
            $table->float('height')->nullable();
            $table->float('fat_percentage')->nullable();
            $table->float('muscle_mass')->nullable();

            // Avaliação corporal
            $table->json('objectives')->nullable();
            $table->json('perimetry')->nullable();
            $table->json('cellulite')->nullable();
            $table->json('flaccidity')->nullable();
            $table->boolean('liquid_retention')->default(false);
            $table->json('body_map_areas')->nullable();

            // Histórico e plano
            $table->json('medical_history')->nullable();
            $table->longText('previous_procedures')->nullable();
            $table->json('treatment_plan')->nullable();
            $table->json('evolution_sessions')->nullable();

            // Fotos
            $table->longText('photo_front')->nullable();
            $table->longText('photo_profile_right')->nullable();
            $table->longText('photo_profile_left')->nullable();

            // Consentimento e assinaturas
            $table->boolean('consent_accepted')->default(false);
            $table->longText('patient_signature')->nullable();
            $table->longText('professional_signature')->nullable();

            // Fluxo de assinatura por token
            $table->string('signature_token')->nullable()->unique();
            $table->timestamp('signature_token_expires_at')->nullable();
            $table->timestamp('signed_at')->nullable();

            $table->softDeletes();
            $table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('body_evaluations');
	}
};

