<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('material_id');
            $table->unsignedBigInteger('qr_code_id')->nullable();
            $table->enum('type', ['received', 'issued', 'transferred', 'used', 'returned', 'damaged', 'lost']);
            $table->integer('quantity');
            $table->unsignedBigInteger('from_location_id')->nullable();
            $table->unsignedBigInteger('to_location_id')->nullable();
            $table->unsignedBigInteger('performed_by');
            $table->text('remarks')->nullable();
            $table->string('reference_number')->nullable();
            $table->timestamps();

            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade');
            $table->foreign('qr_code_id')->references('id')->on('qr_codes')->onDelete('set null');
            $table->foreign('from_location_id')->references('id')->on('locations')->onDelete('set null');
            $table->foreign('to_location_id')->references('id')->on('locations')->onDelete('set null');
            $table->foreign('performed_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
