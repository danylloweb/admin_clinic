<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FacialEvaluationCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'cpf' => 'nullable|string|max:14',
            'email' => 'nullable|email|max:255',
            'whatsapp' => 'nullable|string|max:20',
            'chief_complaint' => 'nullable|string|max:1000',
            'skin_type' => 'nullable|in:normal,oily,dry,mixed,sensitive',
            'oiliness' => 'nullable|integer|between:0,10',
            'hydration' => 'nullable|integer|between:0,10',
            'sensitivity' => 'nullable|integer|between:0,10',
            'acne' => 'nullable|boolean',
            'acne_notes' => 'nullable|string|max:500',
            'melasma' => 'nullable|boolean',
            'melasma_notes' => 'nullable|string|max:500',
            'wrinkles' => 'nullable|boolean',
            'wrinkles_notes' => 'nullable|string|max:500',
            'flaccidity' => 'nullable|boolean',
            'flaccidity_notes' => 'nullable|string|max:500',
            'spots' => 'nullable|boolean',
            'spots_notes' => 'nullable|string|max:500',
            'dilated_pores' => 'nullable|boolean',
            'dilated_pores_notes' => 'nullable|string|max:500',
            'fitzpatrick_type' => 'nullable|in:I,II,III,IV,V,VI',
            'aesthetic_history' => 'nullable|array',
            'allergies' => 'nullable|string|max:1000',
            'medications_in_use' => 'nullable|string|max:1000',
            'patient_objective' => 'nullable|string|max:1000',
            'treatment_plan' => 'nullable|array',
            'photo_front' => 'nullable|string|max:1000',
            'photo_profile_right' => 'nullable|string|max:1000',
            'photo_profile_left' => 'nullable|string|max:1000',
            'consent_accepted' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'patient_id.required' => 'O paciente é obrigatório.',
            'patient_id.exists' => 'O paciente informado não existe.',
            'email.email' => 'O email informado é inválido.',
            'skin_type.in' => 'O tipo de pele informado é inválido.',
            'oiliness.between' => 'A oleosidade deve estar entre 0 e 10.',
            'hydration.between' => 'A hidratação deve estar entre 0 e 10.',
            'sensitivity.between' => 'A sensibilidade deve estar entre 0 e 10.',
            'fitzpatrick_type.in' => 'O fototipo de Fitzpatrick informado é inválido.',
        ];
    }
}
