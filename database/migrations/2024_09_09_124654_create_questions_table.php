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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quiz_id');
            $table->string('question'); // Soal yang di munculkan
            $table->string('option_a'); // Jawaban di pilihan A
            $table->string('option_b'); // Jawaban di pilihan B
            $table->string('option_c'); // Jawaban di pilihan C
            $table->string('option_d'); // Jawaban di pilihan D
            $table->string('correct_answer'); // Jawaban Yang Benar
            $table->timestamps();

            $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questions');
    }
};
