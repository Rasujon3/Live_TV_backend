<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('youtube_api_keys', function (Blueprint $table) {
            $table->id();
            $table->string('api_key')->unique();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('usage_count')->default(0); // optional
            $table->text('comment')->nullable(); // optional
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('youtube_api_keys');
    }
};
