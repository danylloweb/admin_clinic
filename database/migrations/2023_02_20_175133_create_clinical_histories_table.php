<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateClinicalHistoriesTable.
 */
class CreateClinicalHistoriesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('clinical_histories', function(Blueprint $table) {
            $table->increments('id');
            $table->foreignIdFor(\App\Entities\Patient::class);
            $table->enum('type_of_food',['Boa','Regular','Ruim']);
            $table->enum('consume_alcohol',['Sim','As vezes','Não']);
            $table->enum('smoke',['Sim','As vezes','Não']);
            $table->enum('practice_physical_activity',['Sim','As vezes','Não']);
            $table->smallInteger('liters_of_water_per_day')->default(1);
            $table->string('use_medication')->nullable();
            $table->string('have_allergies')->nullable();
            $table->string('use_anabolic_hormones')->nullable();
            $table->string('children')->default(0);
            $table->enum('pacemaker',['Sim','Não']);
            $table->enum('metal_prosthesis',['Sim','Não']);
            $table->enum('diabetes',['Sim','Não']);
            $table->enum('oncology',['Sim','Não']);
            $table->enum('arterial_hypertension',['Sim','Não']);
            $table->enum('blood_type',['A+','B+','AB+','O+','A-','B-','AB-','O-','Outros']);
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
		Schema::drop('clinical_histories');
	}
}
