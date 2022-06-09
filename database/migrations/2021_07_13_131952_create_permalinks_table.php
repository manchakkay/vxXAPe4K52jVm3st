<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermalinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permalinks', function (Blueprint $table) {
            /* ----------------------------------------
                Ключи и индексы
            ---------------------------------------- */
            // Первичный ключ
            $table->bigIncrements('id');


            /* ----------------------------------------
                Внешние ключи
            ---------------------------------------- */
            // Группа вечной ссылки (ID)
            $table->foreignId('permalinks_group_id')
                ->nullable()
                ->default(NULL)

                ->constrained()

                ->onUpdate('cascade')
                ->onDelete('cascade');


            /* ----------------------------------------
                Контент ссылки
            ---------------------------------------- */
            // Название ссылки
            $table->string('content_title', 255);
            // Адрес ссылки
            $table->string('content_link', 1024);


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
        Schema::dropIfExists('permalinks');
    }
}
