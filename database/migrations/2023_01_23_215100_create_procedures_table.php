<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateProceduresTable.
 */
class CreateProceduresTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('procedures', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->smallInteger('procedure_type_id');
            $table->smallInteger('execution_time');
            $table->smallInteger('minimum_amount_of_time')->default(1);
            $table->boolean('non_competing')->default(0);
            $table->decimal('price',10,2);
            $table->decimal('cost_price',10,2);
            $table->integer('percentage_on_sale')->default(150);
            $table->boolean('status')->default(1);
            $table->longText('observation')->nullable();
            $table->longText('step_by_step')->nullable();
            $table->text('patient_instructions')->nullable();
            $table->longText('message_schedule')->nullable();
            $table->integer('author')->nullable();
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
		Schema::drop('procedures');
	}
}
