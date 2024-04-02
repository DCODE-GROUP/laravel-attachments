<?php

namespace Dcodegroup\LaravelAttachments\Http\Requests\Media;

use Duijker\LaravelModelExistsRule\ModelExists;
use Illuminate\Foundation\Http\FormRequest;

class AttachRequest extends FormRequest
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
            'media' => [
                'required',
                'exists:media,id',
            ],
            'modelClass' => [
                'required',
                'string',
            ],
            'modelId' => [
                'required',
                new ModelExists($this->request->get('modelClass'), 'id'),
            ],
        ];
    }
}
