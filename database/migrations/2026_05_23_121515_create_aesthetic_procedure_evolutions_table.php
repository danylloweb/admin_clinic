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
		Schema::create('aesthetic_procedure_evolutions', function(Blueprint $table) {
					$table->increments('id');
					$table->unsignedInteger('schedule_id')->unique();
					$table->unsignedInteger('patient_id')->index();
					$table->unsignedInteger('professional_id')->nullable()->index();
					$table->string('procedure_name')->nullable();
					$table->date('start_date')->nullable();
					$table->json('evolution_sessions')->nullable();
					$table->longText('photo_before')->nullable();
					$table->longText('photo_after')->nullable();
					$table->enum('result_evaluation', ['Excelente', 'Bom', 'Regular', 'Insatisfatorio'])->nullable();
					$table->longText('patient_signature')->nullable();
					$table->longText('professional_signature')->nullable();
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
		Schema::dropIfExists('aesthetic_procedure_evolutions');
	}
};
