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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')
                ->nullable()
                ->constrained('employees')
                ->cascadeOnDelete();
            $table->time('start_time');
            $table->time('end_time')
                ->nullable();
            $table->enum('work_location', ['office', 'anywhere'])
                ->default('office');
            $table->double('longitude')
                ->nullable();
            $table->double('latitude')
                ->nullable();
            $table->string('image_path')
                ->nullable();
            $table->string('task_link')
                ->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
