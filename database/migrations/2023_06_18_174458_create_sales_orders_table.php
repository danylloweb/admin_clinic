<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateSalesOrdersTable.
 */
class CreateSalesOrdersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sales_orders', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('patient_id')->unsigned();
            $table->decimal('amount',10,2)->default(1);
            $table->decimal('discount',10,2);
            $table->integer('author_id')->unsigned();
            $table->smallInteger('sales_order_status_id')->unsigned()->default(1);
            $table->smallInteger('qty')->default(1);
            $table->smallInteger('qty_installments')->default(1);
            $table->smallInteger('type_payment')->default(1);
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
		Schema::drop('sales_orders');
	}
}
