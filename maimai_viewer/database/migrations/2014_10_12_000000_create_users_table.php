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
        if (Schema::hasTable('users')) {

        }
        else {
            Schema::table('users', function($table)
            {
                $table->string('username', 255);
                $table->string('email', 255);
                $table->string('friendcode', 255);
                $table->string('password', 255);
                $table->binary('picture');
                $table->integer('rating');
                $table->string('title', 255);
                $table->integer('playcount');
                $table->string('classrank', 255);
                $table->string('courserank', 255);
                $table->timestamps();
                $table->timestamp('score_updated');
                $table->rememberToken();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
