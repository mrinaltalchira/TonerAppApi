<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\text;

return new class extends Migration
{ 
    public function up(): void
    {
        
        Schema::create('supplychain', function (Blueprint $table) {
            $table->id();
            $table->string('dispatch_receive',10)->nullable();
            $table->string('client_name',255)->nullable();
            $table->string('client_city',255)->nullable();
            $table->string('model_no',255)->nullable();
            $table->string('date_time',255)->nullable();
            $table->string('qr_code')->nullable(); 
            $table->string('reference',255)->nullable();
            $table->string('add_by')->nullable();
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
