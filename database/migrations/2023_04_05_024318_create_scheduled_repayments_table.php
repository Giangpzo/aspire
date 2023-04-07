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
        Schema::create('scheduled_repayments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('loan_id');
            $table->date('repayment_date');
            $table->unsignedBigInteger('amount');
            $table->unsignedTinyInteger('status')->default(0);
            $table->date('actual_repayment_date')->nullable(true);
            $table->unsignedBigInteger('actual_amount')->nullable(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_repayments');
    }
};
