<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGalleryPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gallery_photos', function (Blueprint $table) {
            /* ----------------------------------------
                Ключи и индексы
            ---------------------------------------- */
            // Первичный ключ
            $table->bigIncrements('id');
            // Уникальный слаг для адреса страницы
            $table->string('slug', 128)
                ->unique()

                ->nullable()
                ->default(NULL);


            /* ----------------------------------------
                Булевы флаги
            ---------------------------------------- */
            // Показывать ли фото-галерею
            $table->boolean('is_visible')
                ->default(0);


            /* ----------------------------------------
                Контент фото-галереи
            ---------------------------------------- */
            // Заголовок фото-галереи
            $table->string('content_title', 255);
            // Описание фото-галереи
            $table->text('content_description')
                ->nullable()
                ->default(NULL);


            /* ----------------------------------------
                Временные отметки
            ---------------------------------------- */
            // Когда создана
            $table->timestamp('created_at')
                ->useCurrent();
            // Когда обновлена
            $table->timestamp('updated_at')
                ->nullable()
                ->useCurrentOnUpdate();
            // Когда удалена (если удалена)
            $table->timestamp('deleted_at')
                ->nullable()
                ->default(NULL);
            // Когда опубликована (если опубликована)
            $table->timestamp('published_at')
                ->nullable()
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
        Schema::dropIfExists('gallery_photos');
    }
}
