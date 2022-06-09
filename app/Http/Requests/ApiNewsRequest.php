<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiNewsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|unique:news|max:255',
            'thumbnail' => 'required|string|max:512',
            'is_important' => 'required|boolean',
            'body' => 'required|array|min:1',
            'event_date' => 'date_format:Y-m-d H:i:s',
            'description' => 'string|nullable',
            'body.type' => 'required|exists:App\Models\NewsBlock,id',
            'body.content' => 'required|string',
            'body.order' => 'required|numeric|min:0',
            'category_id' => 'required|exists:App\Models\NewsCategory,id'
        ];
    }
}
