<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserCreateRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'name'              => 'bail|required',
            'email'             => 'required|unique:users|email',
            'cpf'               => 'required|unique:users|min:11|max:12',
            'password'          => 'required|min:6',
            'phone'             => 'required|numeric|unique:users|digits:11',
        ];
    }

    /**
     * @return string[]
     */
    public function messages()
    {
        return [
            'email.required'            => 'O campo e-mail é obrigatório',
            'email.unique'              => 'Esse endereço email já está em uso',
            'email.email'               => 'Esse endereço email está com formato inválido',
            'cpf.required'              => 'O campo CPF ou CNPJ é obrigatório',
            'cpf.unique'                => 'Esse numero de CPF ou CNPJ já está em uso',
            'cpf.min'                   => 'Formato invalido',
            'cpf.max'                   => 'Formato invalido',
            'password.required'         => 'O campo senha é obrigatório',
            'password.min'              => 'O campo senha esta menor que o permitido',
            'phone.required'            => 'Telefone movel é obrigatório',
        ];
    }
}
