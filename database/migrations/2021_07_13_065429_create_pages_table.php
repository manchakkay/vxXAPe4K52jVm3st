<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            /* ----------------------------------------
            Ключи и индексы
            ---------------------------------------- */
            // Первичный ключ
            $table->bigIncrements('id');
            // Уникальный слаг для адреса страницы
            $table->string('slug', 128)
                ->unique()

                ->nullable()
                ->default(null);
            $table->bigInteger('sort')
                ->nullable()
                ->default(null);
            /* ----------------------------------------
            Внешние ключи
            ---------------------------------------- */
            // Категория новости (ID)
            $table->bigInteger('parent_id')
                ->nullable()
                ->unsigned()
                ->default(null)
                ->constrained();
            /* ----------------------------------------
            Булевы флаги
            ---------------------------------------- */
            // Показывать ли страницу
            $table->boolean('is_visible')
                ->default(0);
            // Пройден ли процессинг
            $table->boolean('is_processed')
                ->default(0);
            // Размещена ли в меню
            $table->boolean('is_structured')
                ->default(0);
            $table->string('render_hash', 512)
                ->nullable()
                ->default(null);

            /* ----------------------------------------
            Контент страницы
            ---------------------------------------- */
            // Заголовок страницы
            $table->string('content_title', 255);
            // Обложка страницы
            $table->string('content_thumbnail', 1024)
                ->nullable()
                ->default(null);
            // Описание страницы
            $table->text('content_description')
                ->nullable()
                ->default(null);
            // Контент страницы в JSON формате
            $table->json('content_json')
                ->nullable()
                ->default(null);
            // Контент страницы в HTML формате
            $table->longText('content_html')
                ->nullable()
                ->default(null);

            /* ----------------------------------------
            Мета-информация для OpenGraph
            ---------------------------------------- */
            // Заголовок страницы
            $table->string('meta_title', 255)
                ->nullable()
                ->default(null);
            // Обложка страницы
            $table->string('meta_thumbnail', 1024)
                ->nullable()
                ->default(null);
            // Описание страницы
            $table->text('meta_description')
                ->nullable()
                ->default(null);

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
                ->default(null);
            // Когда опубликована (если опубликована)
            $table->timestamp('published_at')
                ->nullable()
                ->default(null);

            /* ----------------------------------------
            Импортированные новости
            ---------------------------------------- */
            // Флаг импортированных новостей
            $table->boolean('is_imported')
                ->default(0);
            // Импортированный контент
            $table->longText("content_imported")
                ->nullable()
                ->default(null);
            // Предыдущий slug
            $table->text('old_slug')
                ->nullable()
                ->default(null);
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->foreign('parent_id')
                ->references('id')->on('pages')
                ->onUpdate('cascade')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pages');
    }
}
