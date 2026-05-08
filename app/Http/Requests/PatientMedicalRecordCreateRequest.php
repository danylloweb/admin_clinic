<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PatientMedicalRecordCreateRequest extends FormRequest
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
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'treatment_goals' => ['nullable', 'string'],
            'type_of_food' => ['nullable', 'in:Boa,Regular,Ruim'],
            'consume_alcohol' => ['nullable', 'in:Sim,As vezes,Não'],
            'smoke' => ['nullable', 'in:Sim,As vezes,Não'],
            'practice_physical_activity' => ['nullable', 'in:Sim,As vezes,Não'],
            'liters_of_water_per_day' => ['nullable', 'integer', 'min:0', 'max:20'],
            'use_medication' => ['nullable', 'string'],
            'have_allergies' => ['nullable', 'string'],
            'use_anabolic_hormones' => ['nullable', 'string'],
            'children' => ['nullable', 'string', 'max:50'],
            'pacemaker' => ['nullable', 'in:Sim,Não'],
            'metal_prosthesis' => ['nullable', 'in:Sim,Não'],
            'diabetes' => ['nullable', 'in:Sim,Não'],
            'oncology' => ['nullable', 'in:Sim,Não'],
            'arterial_hypertension' => ['nullable', 'in:Sim,Não'],
            'blood_type' => ['nullable', 'in:A+,B+,AB+,O+,A-,B-,AB-,O-,Outros'],
            'observation' => ['nullable', 'string'],
            'signature_name' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'emergency_contact_name.string' => 'O nome do contato deve ser um texto.',
            'emergency_contact_name.max' => 'O nome do contato não pode exceder 255 caracteres.',
            'emergency_contact_phone.string' => 'O telefone do contato deve ser um texto.',
            'emergency_contact_phone.max' => 'O telefone do contato não pode exceder 20 caracteres.',
            'treatment_goals.string' => 'O objetivo do tratamento deve ser um texto.',
            'type_of_food.in' => 'O tipo de alimentação deve ser Boa, Regular ou Ruim.',
            'consume_alcohol.in' => 'O consumo de álcool deve ser Sim, As vezes ou Não.',
            'smoke.in' => 'O fumo deve ser Sim, As vezes ou Não.',
            'practice_physical_activity.in' => 'A atividade física deve ser Sim, As vezes ou Não.',
            'liters_of_water_per_day.integer' => 'Os litros de água devem ser um número inteiro.',
            'liters_of_water_per_day.min' => 'Os litros de água devem ser no mínimo 0.',
            'liters_of_water_per_day.max' => 'Os litros de água não podem exceder 20.',
            'use_medication.string' => 'As medicações devem ser um texto.',
            'have_allergies.string' => 'As alergias devem ser um texto.',
            'use_anabolic_hormones.string' => 'As informações sobre hormônios devem ser um texto.',
            'children.string' => 'As informações sobre filhos devem ser um texto.',
            'children.max' => 'As informações sobre filhos não podem exceder 50 caracteres.',
            'pacemaker.in' => 'O marcapasso deve ser Sim ou Não.',
            'metal_prosthesis.in' => 'A prótese/metais devem ser Sim ou Não.',
            'diabetes.in' => 'O diabetes deve ser Sim ou Não.',
            'oncology.in' => 'O histórico oncológico deve ser Sim ou Não.',
            'arterial_hypertension.in' => 'A hipertensão deve ser Sim ou Não.',
            'blood_type.in' => 'O tipo sanguíneo deve ser um dos valores válidos.',
            'observation.string' => 'As observações devem ser um texto.',
            'lgpd_consent.required' => 'Você deve autorizar o uso das informações para continuar.',
            'lgpd_consent.accepted' => 'Você deve autorizar o uso das informações para continuar.',
            'signature_name.required' => 'O nome para assinatura é obrigatório.',
            'signature_name.string' => 'O nome para assinatura deve ser um texto.',
            'signature_name.max' => 'O nome para assinatura não pode exceder 255 caracteres.',
        ];
    }
}
