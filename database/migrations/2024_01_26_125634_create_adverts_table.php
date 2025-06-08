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
		Schema::create('adverts', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->longText('description');
            $table->string('code');
            $table->string('url_site');
            $table->string('url_checkout');
            $table->smallInteger('status')->default(0);
            $table->smallInteger('qty_click_confirmed')->default(0);
            $table->smallInteger('qty_click_checkout')->default(0);
            $table->smallInteger('qty_convert')->default(0);
            $table->decimal('price_per_click',10,2)->default(0);
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
		Schema::drop('adverts');
	}
};
