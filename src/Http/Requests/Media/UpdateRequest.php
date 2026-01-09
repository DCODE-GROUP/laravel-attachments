<?php

namespace Dcodegroup\LaravelAttachments\Http\Requests\Media;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'file' => [
                'required', 
                'file'
            ],
        ];
    }
}