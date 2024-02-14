<?php

namespace Dcodegroup\LaravelAttachments\Http\Requests\Media;

use Duijker\LaravelModelExistsRule\ModelExists;
use Illuminate\Foundation\Http\FormRequest;

class ExistingRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'category_id' => [
                'nullable',
                'numeric',
            ],
            'modelClass' => [
                'required',
                'string',
            ],
            'modelId' => [
                'required',
                'integer',
                new ModelExists($this->query->get('modelClass', ''), 'id'),
            ],
        ];
    }
}
