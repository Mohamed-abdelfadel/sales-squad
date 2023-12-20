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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string("full_name")->nullable();
            $table->string("email")->unique()->nullable();
            $table->string("phone_number")->unique();
            $table->decimal("value",12,2)->default(0);
            $table->string("company_name")->nullable();
            $table->string("response_time")->nullable();
            $table->string("job_title")->nullable();
            $table->string("address")->nullable();
            $table->string("source")->nullable();
            $table->string("comment", 1000)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
