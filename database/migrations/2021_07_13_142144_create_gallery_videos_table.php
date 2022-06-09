<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGalleryVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gallery_videos', function (Blueprint $table) {
            /* ----------------------------------------
                Ключи и индексы
            ---------------------------------------- */
            // Первичный ключ
            $table->bigIncrements('id');


            /* ----------------------------------------
                Булевы флаги
            ---------------------------------------- */
            // Показывать ли видео
            $table->boolean('is_visible')
                ->default(0);


            /* ----------------------------------------
                Контент видео-галереи
            ---------------------------------------- */
            // Заголовок видео
            $table->string('content_title', 255);
            // Обложка видео
            $table->string('content_thumbnail', 1024);
            // Описание новости
            $table->string('content_link', 1024);


            /* ----------------------------------------
                Временные отметки
            ---------------------------------------- */
            // Когда опубликована (если опубликована)
            $table->timestamp('published_at')
                ->default(NULL);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gallery_videos');
    }
}
