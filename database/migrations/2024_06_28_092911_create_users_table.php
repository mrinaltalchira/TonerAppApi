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
            $table->id('user_id'); // Auto-incrementing ID with custom name
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('is_active')->default(false);
            $table->string('user_role');
            $table->string('password');
            $table->string('machine_module')->default(true);
            $table->string('client_module')->default(true);
            $table->string('user_module')->default(true);
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
