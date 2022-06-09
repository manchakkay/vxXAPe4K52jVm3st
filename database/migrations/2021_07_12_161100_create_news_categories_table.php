<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news_categories', function (Blueprint $table) {
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
                Контент категории
            ---------------------------------------- */
            // Заголовок категории
            $table->string('content_title', 255)
                ->nullable()
                ->default(NULL);
            // Адрес иконки
            $table->string('content_icon', 255)
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
        Schema::dropIfExists('news_categories');
    }
}
