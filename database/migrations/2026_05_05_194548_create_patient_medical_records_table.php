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
		Schema::create('patient_medical_records', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('patient_id')->index();
            $table->string('access_token', 64)->nullable()->unique();
            $table->timestamp('token_generated_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();
            $table->text('treatment_goals')->nullable();
            $table->enum('type_of_food', ['Boa', 'Regular', 'Ruim'])->nullable();
            $table->enum('consume_alcohol', ['Sim', 'As vezes', 'Não'])->nullable();
            $table->enum('smoke', ['Sim', 'As vezes', 'Não'])->nullable();
            $table->enum('practice_physical_activity', ['Sim', 'As vezes', 'Não'])->nullable();
            $table->unsignedSmallInteger('liters_of_water_per_day')->nullable();
            $table->text('use_medication')->nullable();
            $table->text('have_allergies')->nullable();
            $table->text('use_anabolic_hormones')->nullable();
            $table->string('children')->nullable();
            $table->enum('pacemaker', ['Sim', 'Não'])->nullable();
            $table->enum('metal_prosthesis', ['Sim', 'Não'])->nullable();
            $table->enum('diabetes', ['Sim', 'Não'])->nullable();
            $table->enum('oncology', ['Sim', 'Não'])->nullable();
            $table->enum('arterial_hypertension', ['Sim', 'Não'])->nullable();
            $table->enum('blood_type', ['A+', 'B+', 'AB+', 'O+', 'A-', 'B-', 'AB-', 'O-', 'Outros'])->nullable();
            $table->text('observation')->nullable();
            $table->boolean('lgpd_consent')->default(false);
            $table->string('signature_name')->nullable();
            $table->timestamps();
            $table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('patient_medical_records');
	}
};
