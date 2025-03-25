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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->integer('duration')->default(0);
            $table->boolean('is_done')->default(false);
            $table->json('assignees')->default('[]');
            $table->string('name');
            $table->enum('priority', ['alacsony', 'normál', 'magas'])->default('normál');
            $table->date('scheduled_day');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
