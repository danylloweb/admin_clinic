<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('internal_code')->unique()->index();
            $table->string('ean_code')->nullable()->unique()->index();
            $table->string('name')->index();
            $table->string('trade_name')->nullable();
            $table->text('description')->nullable();
            $table->string('category_type')->default('other'); // enum value
            $table->string('subcategory')->nullable();
            $table->string('brand')->nullable();
            $table->string('anvisa_registration')->nullable();
            $table->string('anvisa_process')->nullable();
            $table->string('image_url', 2048)->nullable()/
            // Flags
            $table->boolean('requires_batch_tracking')->default(true);
            $table->boolean('requires_expiration_tracking')->default(true);
            $table->boolean('requires_refrigeration')->default(false);
            $table->boolean('is_injectable')->default(false);
            $table->boolean('requires_patient_tracking')->default(false);

            // Estoque
            $table->string('unit_measure')->default('unit');
            $table->integer('minimum_stock')->default(0);
            $table->integer('ideal_stock')->default(0);
            $table->integer('current_stock')->default(0);
            $table->integer('reserved_stock')->default(0);

            // Armazenamento
            $table->string('storage_location')->nullable();
            $table->string('aisle')->nullable();
            $table->string('cabinet')->nullable();
            $table->string('shelf')->nullable();
            $table->decimal('min_temperature', 5, 2)->nullable();
            $table->decimal('max_temperature', 5, 2)->nullable();
            $table->integer('ideal_humidity')->nullable();

            // Compra
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('invoice_number')->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('receipt_date')->nullable();
            $table->decimal('unit_value', 10, 2)->default(0);
            $table->decimal('sale_value', 10, 2)->default(0);
            $table->decimal('profit_margin', 5, 2)->nullable();

            // Status e auditoria
            $table->boolean('status')->default(true);
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->json('change_log')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category_type', 'status']);
            $table->index(['requires_patient_tracking', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};

