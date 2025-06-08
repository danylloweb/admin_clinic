<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateProcedureTypesTable.
 */
class CreateProcedureTypesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('procedure_types', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });
        \App\Entities\ProcedureType::create(['name' => 'Corporal']);
        \App\Entities\ProcedureType::create(['name' => 'Facial']);
        \App\Entities\ProcedureType::create(['name' => 'Fisioterapêutico']);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('procedure_types');
	}
}
