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
        Schema::create('sales_order', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id')->autoIncrement();
            $table->char('invoice_number', 20)->unique();
            $table->integer('customer_id')->nullable();
            $table->integer('payment_method_id');
            $table->tinyInteger('type')->comment('0 as Offline and 1 as Online');
            $table->bigInteger('total_capital_price');
            $table->bigInteger('total_sell_price');
            $table->bigInteger('discount_price')->default(0);
            $table->bigInteger('grand_sell_price');
            $table->bigInteger('grand_profit_price');
            $table->integer('created_by');
            $table->timestamp('created_at');
            $table->integer('updated_by');
            $table->timestamp('updated_at');
            $table->integer('deleted_by')->nullable();
            $table->timestamp('deleted_at')->nullable();

            // Foreign Key
            $table->foreign('customer_id')->references('id')->on('customer');
            $table->foreign('payment_method_id')->references('id')->on('payment_method');
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
        Schema::dropIfExists('sales_order');
    }
};
