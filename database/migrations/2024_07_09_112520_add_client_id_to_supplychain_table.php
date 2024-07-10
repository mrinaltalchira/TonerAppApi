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
        Schema::table('supplychain', function (Blueprint $table) {
            $table->string('client_id')->after('dispatch_receive')->nullable();
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplychain', function (Blueprint $table) {
            $table->dropColumn('client_id');
        });
    }
};
