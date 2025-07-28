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
        Schema::create('division_accesses', function (Blueprint $table) {
            $table->foreignId('division_id')->constrained('divisions')->cascadeOnDelete();
            $table->foreignId('access_id')->constrained('accesses')->cascadeOnDelete();
            $table->primary(['division_id', 'access_id']);
            $table->boolean('is_deleted')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('division_accesses');
    }
};
