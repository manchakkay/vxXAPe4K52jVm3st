<?php

use App\Http\Controllers\Admin\Admin_MainController;
//
use App\Http\Controllers\Admin\File\File_MainController;
//
use App\Http\Controllers\Admin\File\File_StorageContoller;
use App\Http\Controllers\Admin\Gallery\Gallery_MainController;
//
use App\Http\Controllers\Admin\Gallery\Gallery_PhotoController;
use App\Http\Controllers\Admin\Gallery\Gallery_VideoController;
//
use App\Http\Controllers\Admin\Link\Link_ContactController;
use App\Http\Controllers\Admin\Link\Link_MainController;
//
use App\Http\Controllers\Admin\Link\Link_SocialController;
use App\Http\Controllers\Admin\Misc\Misc_MainController;
use App\Http\Controllers\Admin\Misc\Misc_PermaLinkController;
//
use App\Http\Controllers\Admin\News\News_CategoryController;
use App\Http\Controllers\Admin\News\News_MainController;
use App\Http\Controllers\Admin\Page\Page_MainController;
use App\Http\Controllers\Admin\Page\Page_NavigationController;
use App\Http\Controllers\Home\Home_MainController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Настройки авторизации
Auth::routes([
    'register' => false,
    'reset' => false,
    'verify' => false,
]);

// Выход из аккаунта
Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

// Панель администратора
Route::prefix("admin")->name('admin')->middleware('auth')->group(function () {

    // Новости
    Route::prefix('news')->name('.news')->group(function () {

        // Редактор страниц
        Route::prefix('redact')->name('.redact')->group(function () {
            Route::get('/{id}', [News_MainController::class, 'redactGet']);
            Route::post('/{id}', [News_MainController::class, 'redactPost'])->name("/post");
            Route::patch('/{id}', [News_MainController::class, 'redactPatch'])->name("/patch");
            Route::delete('/{id}', [News_MainController::class, 'redactDelete'])->name("/delete");
        });

        // Категории новостей
        Route::prefix('categories')->name('.categories')->group(function () {
            Route::get('/', [News_CategoryController::class, 'get']);
            Route::post('/', [News_CategoryController::class, 'post'])->name('/post');
            Route::put('/{news_category_id}', [News_CategoryController::class, 'put'])->name('/put');
            Route::delete('/{news_category_id}', [News_CategoryController::class, 'delete'])->name('/delete');
        });

        // Собственные
        Route::get('/', [News_MainController::class, 'get']);
        Route::post('/', [News_MainController::class, 'post'])->name('/post');
        Route::patch('/', [News_MainController::class, 'patch'])->name('/patch');
        Route::delete('/delete/{id}', [News_MainController::class, 'delete'])->defaults('action', 'delete')->name('/delete');
        Route::delete('/force/{id}', [News_MainController::class, 'delete'])->defaults('action', 'force')->name('/force');
        Route::delete('/restore/{id}', [News_MainController::class, 'delete'])->defaults('action', 'restore')->name('/restore');
    });

    // Страницы
    Route::prefix('pages')->name('.pages')->group(function () {

        // Редактор страниц
        Route::prefix('redact')->name('.redact')->group(function () {
            Route::get('/{id}', [Page_MainController::class, 'redactGet']);
            Route::post('/{id}', [Page_MainController::class, 'redactPost'])->name("/post");
            Route::patch('/{id}', [Page_MainController::class, 'redactPatch'])->name("/patch");
            Route::delete('/{id}', [Page_MainController::class, 'redactDelete'])->name("/delete");
        });

        // Структура навигации
        Route::prefix('navigations')->name('.navigations')->group(function () {
            Route::get('/', [Page_NavigationController::class, 'get']);
            Route::post('/', [Page_NavigationController::class, 'post'])->name('/post');
            Route::put('/{navigation_id}', [Page_NavigationController::class, 'put'])->name('/put');
            Route::delete('/{navigation_id}', [Page_NavigationController::class, 'delete'])->name('/delete');
        });

        Route::get('/', [Page_MainController::class, 'get']);
        Route::post('/', [Page_MainController::class, 'post'])->name('/post');
        Route::patch('/', [Page_MainController::class, 'patch'])->name('/patch');
        Route::delete('/delete/{id}', [Page_MainController::class, 'delete'])->defaults('action', 'delete')->name('/delete');
        Route::delete('/force/{id}', [Page_MainController::class, 'delete'])->defaults('action', 'force')->name('/force');
        Route::delete('/restore/{id}', [Page_MainController::class, 'delete'])->defaults('action', 'restore')->name('/restore');
    });

    // Дополнительно
    Route::prefix('misc')->name('.misc')->group(function () {

        // Вечные ссылки
        Route::prefix('permalinks')->name('.permalinks')->group(function () {
            Route::get('/', [Misc_PermaLinkController::class, 'get']);
            Route::post('/', [Misc_PermaLinkController::class, 'post'])->name("/post");
            Route::put('/{permalink_id}', [Misc_PermaLinkController::class, 'put'])->name("/put");
            Route::delete('/{permalink_id}', [Misc_PermaLinkController::class, 'delete'])->name("/delete");
        });

        Route::get('/', [Misc_MainController::class, 'get']);
    });

    // Ссылки
    Route::prefix('links')->name('.links')->group(function () {

        // Контакты
        Route::prefix('contacts')->name('.contacts')->group(function () {
            Route::get('/', [Link_ContactController::class, 'get']);
            Route::post('/', [Link_ContactController::class, 'post'])->name("/post");
            Route::put('/{contact_id}', [Link_ContactController::class, 'put'])->name("/put");
            Route::delete('/{contact_id}', [Link_ContactController::class, 'delete'])->name("/delete");
        });

        // Соцсети
        Route::prefix('socials')->name('.socials')->group(function () {
            Route::get('/', [Link_SocialController::class, 'get']);
            Route::post('/', [Link_SocialController::class, 'post'])->name("/post");
            Route::put('/{social_id}', [Link_SocialController::class, 'put'])->name("/put");
            Route::delete('/{social_id}', [Link_SocialController::class, 'delete'])->name("/delete");
        });

        Route::get('/', [Link_MainController::class, 'get']);
    });

    // Ссылки
    Route::prefix('files')->name('.files')->group(function () {
        Route::prefix('storage')->name('.storage')->group(function () {
            Route::get('/', [File_StorageContoller::class, 'get']);
            Route::post('/', [File_StorageContoller::class, 'post'])->name('/post');
            Route::patch('/{id}', [File_StorageContoller::class, 'patch'])->name('/patch');
            Route::delete('/delete/{id}', [File_StorageContoller::class, 'delete'])->name('/delete');
            Route::post('/delete_force/{id}', [File_StorageContoller::class, 'forceDelete'])->name('/force');
            Route::post('/delete_restore/{id}', [File_StorageContoller::class, 'restore'])->name('/restore');
        });

        Route::get('/', [File_MainController::class, 'get']);
        Route::post('/', [File_MainController::class, 'post'])->name('/post');
        Route::patch('/', [File_MainController::class, 'patch'])->name('/patch');
        Route::delete('/delete/{id}', [File_MainController::class, 'delete'])->defaults('action', 'delete')->name('/delete');
        Route::delete('/force/{id}', [File_MainController::class, 'delete'])->defaults('action', 'force')->name('/force');
        Route::delete('/restore/{id}', [File_MainController::class, 'delete'])->defaults('action', 'restore')->name('/restore');
    });

    // Пользователи
    Route::prefix('users')->name('.users')->group(function () {
        Route::get('/', [File_MainController::class, 'get']);
        Route::post('/', [File_MainController::class, 'post'])->name('/post');
        Route::patch('/', [File_MainController::class, 'patch'])->name('/patch');
        Route::delete('/delete/{id}', [File_MainController::class, 'delete'])->defaults('action', 'delete')->name('/delete');
        Route::delete('/force/{id}', [File_MainController::class, 'delete'])->defaults('action', 'force')->name('/force');
        Route::delete('/restore/{id}', [File_MainController::class, 'delete'])->defaults('action', 'restore')->name('/restore');
    });

    // Медиа-галереи
    Route::prefix('galleries')->name('.galleries')->group(function () {

        // Видео-галереи
        Route::prefix('videos')->name('.videos')->group(function () {
            Route::get('/', [Gallery_VideoController::class, 'get']);
            Route::post('/', [Gallery_VideoController::class, 'post'])->name('/post');
            Route::patch('/', [Gallery_VideoController::class, 'patch'])->name('/patch');
            Route::delete('/delete/{id}', [Gallery_VideoController::class, 'delete'])->defaults('action', 'delete')->name('/delete');
            Route::delete('/force/{id}', [Gallery_VideoController::class, 'delete'])->defaults('action', 'force')->name('/force');
            Route::delete('/restore/{id}', [Gallery_VideoController::class, 'delete'])->defaults('action', 'restore')->name('/restore');
        });

        // Фото-галереи
        Route::prefix('photos')->name('.photos')->group(function () {
            Route::get('/', [Gallery_PhotoController::class, 'get']);
            Route::post('/', [Gallery_PhotoController::class, 'post'])->name('/post');
            Route::patch('/', [Gallery_PhotoController::class, 'patch'])->name('/patch');
            Route::delete('/delete/{id}', [Gallery_PhotoController::class, 'delete'])->defaults('action', 'delete')->name('/delete');
            Route::delete('/force/{id}', [Gallery_PhotoController::class, 'delete'])->defaults('action', 'force')->name('/force');
            Route::delete('/restore/{id}', [Gallery_PhotoController::class, 'delete'])->defaults('action', 'restore')->name('/restore');
        });

        Route::get('/', [Gallery_MainController::class, 'get']);
    });

    Route::get('/', [Admin_MainController::class, 'get']);
});

// Главная страница
Route::name('home')->middleware('auth')->group(function () {
    Route::get('/', [Home_MainController::class, 'get']);
});

// Главная страница
Route::prefix('news')->name('news')->middleware('auth')->group(function () {

    // Вывод новости
    Route::get('{slug}', [News_MainController::class, 'user_get'])->name('/get');
});

// Файлы
Route::get('/file/{slug}.{ext}', [File_StorageContoller::class, 'get_direct'])->name('files_direct');
Route::get('/file/{slug}', [File_StorageContoller::class, 'get_direct'])->name('files_direct');
Route::get('/file', [File_StorageContoller::class, 'get_direct'])->name('files_direct');
Route::get('/file/dl/{key}', [File_StorageContoller::class, 'files_download'])->name('files_download');

// Страницы
Route::name('pages')->middleware('auth')->group(function () {
    Route::get('/pages', [Page_MainController::class, 'user_get_all']);
    Route::get('/_preview/{id}', [TestController::class, 'preview'])->name('/preview');
    Route::get('/{slugs}', [Page_MainController::class, 'user_get'])->where('slugs', '.*')->name('/get');

});
