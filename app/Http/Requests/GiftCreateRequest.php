<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GiftCreateRequest extends FormRequest
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
            'name'                  => ['required', 'string', 'max:255'],
            'phone'                 => ['required', 'string', 'max:30'],
            'phoneFormatted'        => ['nullable', 'string', 'max:30'],
            'procedureId'           => ['required', 'integer'],
            'procedureName'         => ['required', 'string', 'max:255'],
            'procedureValue'        => ['required', 'numeric'],
            'source'                => ['nullable', 'string', 'max:255'],
            'page'                  => ['nullable', 'string', 'max:2048'],
        ];
    }
}

