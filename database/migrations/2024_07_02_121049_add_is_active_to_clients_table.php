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
        if (!Schema::hasColumn('clients', 'isActive')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->string('isActive')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        if (!Schema::hasColumn('clients', 'isActive')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->dropColumn('isActive');
            });
        }

      
    }
};
