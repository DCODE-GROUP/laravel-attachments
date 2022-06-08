<?php

namespace Dcodegroup\LaravelAttachments\Http\Requests\Annotations;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnnotationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'annotations' => [
                'required',
                'array',
            ],
            'annotations.*.content' => [
                'required',
                'string',
            ],
            'annotations.*.hidden' => ['required'],
        ];
    }
}
