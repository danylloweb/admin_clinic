<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateScreeningsTable.
 */
class CreateScreeningsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('screenings', function(Blueprint $table) {
            $table->increments('id');
            $table->foreignIdFor(\App\Entities\Patient::class);
            $table->enum('pregnant',['Sim','Não'])->default('Não');
            $table->enum('tanned_skin',['Sim','Não']);
            $table->enum('consume_alcohol',['Sim','Não']);
            $table->enum('sour_cream',['Sim','Não']);
            $table->enum('face_lotion',['Sim','Não']);
            $table->string('arterial_tension',6)->nullable();
            $table->smallInteger('weight')->nullable();
            $table->smallInteger('glucose')->nullable();
            $table->smallInteger('imc')->nullable();
            $table->longText('observation')->nullable();
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
		Schema::drop('screenings');
	}
}
