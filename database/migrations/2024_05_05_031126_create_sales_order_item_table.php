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
        Schema::create('sales_order_item', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id')->autoIncrement();
            $table->integer('product_size_id');
            $table->integer('sales_order_id');
            $table->bigInteger('qty');
            $table->bigInteger('capital_price');
            $table->bigInteger('sell_price');
            $table->bigInteger('discount_price')->default(0);
            $table->bigInteger('total_sell_price');
            $table->bigInteger('total_profit_price');
            $table->integer('created_by');
            $table->timestamp('created_at');
            $table->integer('updated_by');
            $table->timestamp('updated_at');
            $table->integer('deleted_by')->nullable();
            $table->timestamp('deleted_at')->nullable();

            // Foreign Key
            $table->foreign('product_size_id')->references('id')->on('product_size');
            $table->foreign('sales_order_id')->references('id')->on('sales_order');
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
        Schema::dropIfExists('sales_order_item');
    }
};
