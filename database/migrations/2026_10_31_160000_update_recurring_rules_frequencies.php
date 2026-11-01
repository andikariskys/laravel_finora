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
        Schema::table('recurring_rules', function (Blueprint $table) {
            $table->string('frequency')->default('monthly')->after('amount'); // daily, weekly, monthly, custom_date
            $table->string('frequency_value')->nullable()->after('frequency'); // day name, day of month, or date
            $table->date('due_date')->nullable()->after('frequency_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recurring_rules', function (Blueprint $table) {
            $table->dropColumn(['frequency', 'frequency_value', 'due_date']);
        });
    }
};
