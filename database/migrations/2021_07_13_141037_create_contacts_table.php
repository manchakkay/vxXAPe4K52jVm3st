<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            /* ----------------------------------------
            Ключи и индексы
            ---------------------------------------- */
            // Первичный ключ
            $table->bigIncrements('id');

            /* ----------------------------------------
            Булевы флаги
            ---------------------------------------- */
            // Показывать ли контакт
            $table->boolean('is_visible')
                ->default(0);

            /* ----------------------------------------
            Контент новости
            ---------------------------------------- */
            // Название контакта
            $table->string('content_title', 255);
            // Текст ссылки контакта
            $table->string('content_body', 255);
            // Ссылка контакта
            $table->string('content_link', 1024);
            // Тип контакта
            $table->string('content_type', 255);
            // Адрес иконки
            $table->string('content_icon', 1024)
                ->nullable()
                ->default(null);

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
        Schema::dropIfExists('contacts');
    }
}
