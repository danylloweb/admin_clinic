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
		Schema::create('facial_evaluations', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('patient_id')->index();
            $table->unsignedInteger('professional_id')->nullable()->index();
            // Avaliacao
            $table->longText('chief_complaint')->nullable();
            $table->enum('skin_type', ['normal', 'oily', 'dry', 'mixed', 'sensitive'])->nullable();
            // Escalas
            $table->unsignedTinyInteger('oiliness')->nullable();
            $table->unsignedTinyInteger('hydration')->nullable();
            $table->unsignedTinyInteger('sensitivity')->nullable();
            // Condicoes da pele
            $table->boolean('acne')->default(false);
            $table->text('acne_notes')->nullable();
            $table->boolean('melasma')->default(false);
            $table->text('melasma_notes')->nullable();
            $table->boolean('wrinkles')->default(false);
            $table->text('wrinkles_notes')->nullable();
            $table->boolean('flaccidity')->default(false);
            $table->text('flaccidity_notes')->nullable();
            $table->boolean('spots')->default(false);
            $table->text('spots_notes')->nullable();
            $table->boolean('dilated_pores')->default(false);
            $table->text('dilated_pores_notes')->nullable();
            // Fitzpatrick e historico
            $table->enum('fitzpatrick_type', ['I', 'II', 'III', 'IV', 'V', 'VI'])->nullable();
            $table->json('aesthetic_history')->nullable();
            // Clinico e plano
            $table->longText('allergies')->nullable();
            $table->longText('medications_in_use')->nullable();
            $table->longText('patient_objective')->nullable();
            $table->json('treatment_plan')->nullable();
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
		Schema::dropIfExists('facial_evaluations');
	}
};
