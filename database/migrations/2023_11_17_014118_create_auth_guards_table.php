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
        Schema::create('auth_guard_otp_codes', function (Blueprint $table) {

            $table->id();
            $table->text("access_token")->nullable();
            $table->string("reference")->nullable();
            $table->ipAddress()->nullable();

            $table->integer("number_digits")->default(6);
            $table->string("code");
            $table->string("phone");
            $table->string("email");

            $table->integer("attempts_left");
            $table->timestamp("expires_at");
            $table->boolean("confirmed");

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auth_guard_otp_codes');
    }
};
