<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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
            'password' => 'required|string|max:25|confirmed',
            'token'    => 'required|exists:password_resets,token'
        ];
    }

    /**
     * @return array|string[]
     */
    public function messages()
    {
        return [
            'password.required'  => 'Campo senha Obrigatório',
            'password.string'    => 'Formato Incorreto',
            'password.max'       => 'Tamanho excedido',
            'password.confirmed' => 'Campos não coincidem',
            'token.required'     => 'Esta faltando o token',
            'token.exists'       => 'Token invalido',
        ];
    }
}
