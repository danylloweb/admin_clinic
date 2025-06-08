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
		Schema::create('follow_up_messages', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('message');
            $table->enum('status',["Ativo","Inativo"])->default("Ativo");
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
		Schema::drop('follow_up_messages');
	}
};
