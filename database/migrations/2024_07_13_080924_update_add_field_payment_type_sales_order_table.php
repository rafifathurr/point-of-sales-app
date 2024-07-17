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
        Schema::table('sales_order', function (Blueprint $table) {
            $table->integer('payment_method_id')->nullable()->change();
            $table->tinyInteger('payment_type')->default(0)->comment('0 as Payment Cash, 1 as Payment Point and 2 as Payment Cash and Point')->change();
            $table->bigInteger('total_point');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
