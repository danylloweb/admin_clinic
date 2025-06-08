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
		Schema::create('follow_up_schedule_messages', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('patient_id')->unsigned();
            $table->integer('sales_order_id')->unsigned()->nullable();
            $table->integer('follow_up_schedule_id')->unsigned();
            $table->integer('follow_up_message_id')->unsigned()->nullable();
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->enum('status',["Agendado","Em processamento","Enviado","Cancelado"])->default("Agendado");
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
		Schema::drop('follow_up_schedule_messages');
	}
};
