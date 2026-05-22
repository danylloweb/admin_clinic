<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('facial_evaluations')) {
            return;
        }

        Schema::create('facial_evaluations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('patient_id')->index();
            $table->unsignedInteger('professional_id')->nullable()->index();
            $table->longText('chief_complaint')->nullable();
            $table->enum('skin_type', ['normal', 'oily', 'dry', 'mixed', 'sensitive'])->nullable();
            $table->unsignedTinyInteger('oiliness')->nullable();
            $table->unsignedTinyInteger('hydration')->nullable();
            $table->unsignedTinyInteger('sensitivity')->nullable();
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
            $table->enum('fitzpatrick_type', ['I', 'II', 'III', 'IV', 'V', 'VI'])->nullable();
            $table->json('aesthetic_history')->nullable();
            $table->longText('allergies')->nullable();
            $table->longText('medications_in_use')->nullable();
            $table->longText('patient_objective')->nullable();
            $table->json('treatment_plan')->nullable();
            $table->longText('photo_front')->nullable();
            $table->longText('photo_profile_right')->nullable();
            $table->longText('photo_profile_left')->nullable();
            $table->boolean('consent_accepted')->default(false);
            $table->longText('patient_signature')->nullable();
            $table->longText('professional_signature')->nullable();
            $table->string('signature_token')->nullable()->unique();
            $table->timestamp('signature_token_expires_at')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facial_evaluations');
    }
};

