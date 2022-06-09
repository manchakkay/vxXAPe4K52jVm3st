<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSocialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('socials', function (Blueprint $table) {
            /* ----------------------------------------
                Ключи и индексы
            ---------------------------------------- */
            // Первичный ключ
            $table->bigIncrements('id');


            /* ----------------------------------------
                Булевы флаги
            ---------------------------------------- */
            // Показывать ли соцсеть
            $table->boolean('is_visible')
                ->default(0);


            /* ----------------------------------------
                Контент новости
            ---------------------------------------- */
            // Название соцсети
            $table->string('content_title', 255);
            // Ссылка на соцсеть
            $table->string('content_link', 1024);
            // Адрес иконки
            $table->string('content_icon', 1024)
                ->nullable()
                ->default(NULL);


            /* ----------------------------------------
                Сортировка и группировка
            ---------------------------------------- */
            // Сортировка по номеру
            $table->integer('sort_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('socials');
    }
}
