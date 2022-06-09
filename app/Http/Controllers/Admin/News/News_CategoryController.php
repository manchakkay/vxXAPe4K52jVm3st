<?php

namespace App\Http\Controllers\Admin\News;

use App\Http\Controllers\_Templates\Controller;
use App\Models\NewsCategory;
use Illuminate\Http\Request;

class News_CategoryController extends Controller
{
    // > Список категорий
    public function get()
    {
        return view(
            'admin.news.categories',
            [
                'news_categories' => NewsCategory::all(),
            ]
        );
    }

    public function post(Request $req)
    {
        // Валидация
        $validated = $req->validate([
            'content_title' => [
                'required',
                'string',
                'max: 127',
            ],
        ]);

        // Создаём объект
        $news_category = new NewsCategory;
        // Заполняем данными
        $news_category
            ->fill([
                'content_title' => $validated['content_title'],
            ])
            ->save();

        // Переход обратно
        return redirect()->action([News_CategoryController::class, 'get']);
    }

    // > Создание категории
    public function createCategories(Request $req)
    {
        // Валидация
        $validated = $req->validate([
            'content_title' => [
                'required',
                'string',
                'max: 127',
            ],
        ]);

        // Создаём объект
        $news_category = new NewsCategory;
        // Заполняем данными
        $news_category
            ->fill([
                'content_title' => $validated['content_title'],
            ])
            ->save();

        // Переход обратно
        return redirect()->action([News_CategoryController::class, 'readCategories']);
    }

    // > Удаление категорий
    public function deleteCategories(int $news_category_id)
    {
        // Валидация
        if (NewsCategory::where('id', $news_category_id)->exists()) {
            $news_category = NewsCategory::where('id', $news_category_id)->first();
            $news_category->delete();
        }

        // Переход обратно
        return redirect()->action([News_CategoryController::class, 'readCategories']);
    }
}
