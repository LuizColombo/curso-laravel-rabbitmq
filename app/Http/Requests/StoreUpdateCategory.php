<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateCategory extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $url = $this->segment(2); // $this->url;

        return [
            'title' => "required|min:3|max:150|unique:categories,title,{$url},url",
            'description' => 'required|min:3|max:255',
        ];
    }
}
