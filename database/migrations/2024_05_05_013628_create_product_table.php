<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id')->autoIncrement();
            $table->integer('category_product_id');
            $table->integer('supplier_id');
            $table->string('name');
            $table->text('picture')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->integer('created_by');
            $table->timestamp('created_at');
            $table->integer('updated_by');
            $table->timestamp('updated_at');
            $table->integer('deleted_by')->nullable();
            $table->timestamp('deleted_at')->nullable();

            // Foreign Key
            $table->foreign('category_product_id')->references('id')->on('category_product');
            $table->foreign('supplier_id')->references('id')->on('supplier');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('deleted_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product');
    }
};
