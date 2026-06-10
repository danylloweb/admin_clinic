<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create('procedure_consumptions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('product_lot_id')->nullable()->index();
            $table->unsignedInteger('patient_id')->index();
            $table->unsignedBigInteger('aesthetic_procedure_evolution_id')->index();
            $table->unsignedBigInteger('professional_id')->nullable();
            $table->integer('quantity_used');
            $table->dateTime('consumption_date');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['patient_id', 'consumption_date']);
            $table->index(['product_id', 'consumption_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('procedure_consumptions');
    }
};

