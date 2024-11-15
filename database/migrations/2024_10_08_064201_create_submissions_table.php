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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_task_id')->constrained('project_tasks')->onDelete('cascade'); // Relasi ke tugas proyek
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Relasi ke user (siswa)
            $table->string('file')->nullable(); // File yang diunggah siswa
            $table->string('link')->nullable(); // Link yang diberikan siswa
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
        Schema::dropIfExists('submissions');
    }
};
