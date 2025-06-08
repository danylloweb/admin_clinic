<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateSchedulesTable.
 */
class CreateSchedulesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('schedules', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('procedure_id')->unsigned();
            $table->integer('patient_id')->unsigned();
            $table->date('date');
            $table->time('time');
            $table->timestamps();
            $table->foreign('procedure_id')->references('id')->on('procedures');
            $table->foreign('patient_id')->references('id')->on('patients');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('schedules');
	}
}
