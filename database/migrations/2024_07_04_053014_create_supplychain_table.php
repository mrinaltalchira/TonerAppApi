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
        Schema::create('supplychain', function (Blueprint $table) {
            $table->id();
            $table->string('dispatch_receive');
            $table->string('client_name');
            $table->string('model_no');
            $table->string('date_time');
            $table->json('qr_code'); 
            $table->string('reference');
            $table->string('add_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplychain');
    }
};
