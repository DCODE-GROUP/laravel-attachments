<?php

namespace Dcodegroup\LaravelAttachments\Http\Requests\Media;

use Duijker\LaravelModelExistsRule\ModelExists;
use Illuminate\Foundation\Http\FormRequest;

class UploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'file' => ['required', 'file'],
            'modelClass' => ['required', 'string'],
            'modelId' => [
                'required',
                new ModelExists($this->request->get('modelClass'), 'id'),
            ],
        ];
    }
}
