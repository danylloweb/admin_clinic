<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
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
            'email' => 'required|email|exists:users,email|unique:password_resets'
        ];
    }

    /**
     * @return array|string[]
     */
    public function messages()
    {
        return [
            'email.required' => 'Campo Obrigatório',
            'email.email'    => 'Endereço de email Invalido',
            'email.exists'   => 'Email não existe',
            'email.unique'   => 'Recuperação de senha já foi solicitado com esse endereço de email!',
        ];
    }
}
