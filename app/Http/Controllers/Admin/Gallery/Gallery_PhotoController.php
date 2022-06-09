<?php

namespace App\Http\Controllers\Admin\Gallery;

use App\Http\Controllers\Admin\File\File_StorageContoller;
use App\Http\Controllers\Support\Support_TextController;
use App\Http\Controllers\_Templates\InstanceController;
use App\Models\GalleryPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Gallery_PhotoController extends InstanceController
{
    // Инициализация
    public function __construct()
    {
        parent::__construct(
            model:GalleryPhoto::class,
            view:'admin.galleries.photos',
            actions:[
                'post' => ['createGalleryPhoto'],
                'patch' => ['editGalleryPhoto', 'importGalleryPhoto'],
                'put' => [],
            ],
            filter:[
                "search_col" => 'content_title',
                "paginate" => 8,
                "fulltext" => true,
            ],
        );
    }

    /*
    ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
    Частные методы
    ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
     *///

    // Создание новостей
    protected function post_createGalleryPhoto(Request $req)
    {
        $rules = [
            'cgp_title' => [
                'required',
                'string',
                'max: 100',
            ],
            'cgp_desc' => [
                'required',
                'string',
                'max: 200',
            ],
        ];

        $messages = [
            // Заголовок
            'cgp_title.required' => 'required',
            'cgp_title.string' => 'wrong_type',
            'cgp_title.max' => 'too_big',
            // Описание
            'cgp_desc.required' => 'required',
            'cgp_desc.string' => 'wrong_type',
            'cgp_desc.max' => 'too_big',
        ];

        $req->validate($rules, $messages);

        // Создаём объект
        $instance = new GalleryPhoto;
        // Заполняем текстовые данные
        $instance
            ->fill([
                'content_title' => Support_TextController::typographer($req->get('cgp_title'), true),
                'content_description' => Support_TextController::typographer($req->get('cgp_desc')),
            ])
            ->save();

        // Сохраняем изображения
        $keys = array_keys($req->all());
        $with_files = false;
        $index = 0;
        $messages = [
            'cgp_list.required' => 'required',
            'cgp_list.image' => 'wrong_type',
            'cgp_list.max' => 'too_big',
            'cgp_list.mimes' => 'wrong_format',
        ];
        $rules = [
            'cgp_list' => [
                'required',
                'image',
                'max:10240',
                'mimes:jpg,png',
            ],
        ];

        foreach ($keys as $key) {
            if (substr($key, 0, 9) == "cgp_image") {
                $data = ['cgp_list' => $req->file($key)];
                Validator::validate($data, $rules, $messages);

                File_StorageContoller::storeFileFromRequest($req->file($key), 'gallery_photo', $instance->id, false, null, null, $index);

                $with_files = true;
                $index++;
            }
        }

        if ($with_files) {
            return [
                "status_code" => 302,
                "data" => [],
            ];
        } else {
            return [
                "status_code" => 422,
                "data" => ["errors" => [
                    'cgp_list' => ['required'],
                ]],
            ];
        }

    }
}
