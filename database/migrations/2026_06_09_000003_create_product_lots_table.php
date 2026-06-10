<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_lots', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id')->index();
            $table->string('batch_number')->index();
            $table->date('manufacture_date')->nullable();
            $table->date('expiration_date')->index();
            $table->integer('quantity_received');
            $table->integer('quantity_available')->default(0);
            $table->date('received_date');
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('status')->default('normal'); // ProductLotStatusEnum
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['product_id', 'batch_number']);
            $table->index(['expiration_date', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_lots');
    }
};

