<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('is_active')->default('1');
            $table->string('user_role')->nullable();
            $table->string('token')->nullable();
            $table->string('password')->nullable();
            $table->string('machine_module')->default('1');
            $table->string('client_module')->default('1');
            $table->string('user_module')->default('1');
            $table->timestamps(); // Adds created_at and updated_at columns
        });
    } 

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
