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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reporter_id');
            $table->unsignedBigInteger('reported_user_id')->nullable();
            $table->unsignedBigInteger('reportable_id')->nullable();
            $table->string('reportable_type')->nullable();
            $table->enum('report_subject', ['spam', 'harassment', 'hate_speech', 'violence', 'nudity', 'false_information', 'other']);
            $table->enum('status', ['pending', 'reviewed', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->foreign('reporter_id')->references('id')->on('users');
            $table->foreign('reported_user_id')->references('id')->on('users');
            $table->foreign('reviewed_by')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
