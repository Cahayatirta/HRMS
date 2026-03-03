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
        Schema::table('meetings', function (Blueprint $table) {
            // First, migrate existing data by combining date + time into the time columns
            \DB::statement("UPDATE meetings SET start_time = CONCAT(date, ' ', start_time), end_time = CONCAT(date, ' ', end_time)");
            
            // Now drop the date column
            $table->dropColumn('date');
            
            // Change start_time and end_time from TIME to DATETIME
            $table->dateTime('start_time')->change();
            $table->dateTime('end_time')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            // Add back the date column
            $table->date('date')->after('meeting_note');
            
            // Extract date from start_time into the date column, and keep only time in start_time/end_time
            \DB::statement("UPDATE meetings SET date = DATE(start_time), start_time = TIME(start_time), end_time = TIME(end_time)");
            
            // Change start_time and end_time back to TIME
            $table->time('start_time')->change();
            $table->time('end_time')->nullable()->change();
        });
    }
};
