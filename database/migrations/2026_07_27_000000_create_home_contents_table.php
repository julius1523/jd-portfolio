<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('home_contents', function (Blueprint $table) {
            $table->id();
            $table->text('profile_image')->nullable();
            $table->string('heading')->nullable();
            $table->string('subheading')->nullable();
            $table->text('description')->nullable();
            $table->string('primary_btn_text')->nullable();
            $table->string('primary_btn_link')->nullable();
            $table->string('secondary_btn_text')->nullable();
            $table->text('secondary_btn_file')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_contents');
    }
};
