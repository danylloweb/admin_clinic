<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

/**
 * Class FacialEvaluationValidator.
 *
 * @package namespace App\Validators;
 */
class FacialEvaluationValidator extends LaravelValidator
{
    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'patient_id' => 'required|integer|exists:patients,id',
            'professional_id' => 'nullable|integer|exists:users,id',
            'cpf' => 'nullable|string|max:14',
            'email' => 'nullable|email|max:255',
            'whatsapp' => 'nullable|string|max:20',
            'chief_complaint' => 'required|string|max:2000',
            'skin_type' => 'required|in:normal,oily,dry,mixed,sensitive',
            'oiliness' => 'required|integer|between:0,10',
            'hydration' => 'required|integer|between:0,10',
            'sensitivity' => 'required|integer|between:0,10',
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
            'treatment_plan.procedure' => 'nullable|string|max:255',
            'treatment_plan.sessions' => 'nullable|integer|min:0|max:100',
            'treatment_plan.frequency' => 'nullable|string|max:255',
            'treatment_plan.observations' => 'nullable|string|max:1000',
            'photo_front' => 'nullable|url|max:5000',
            'photo_profile_right' => 'nullable|url|max:5000',
            'photo_profile_left' => 'nullable|url|max:5000',
            'consent_accepted' => 'required|boolean|in:1,true',
        ],
        ValidatorInterface::RULE_UPDATE => [
            'patient_id' => 'sometimes|integer|exists:patients,id',
            'professional_id' => 'nullable|integer|exists:users,id',
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
            'treatment_plan.procedure' => 'nullable|string|max:255',
            'treatment_plan.sessions' => 'nullable|integer|min:0|max:100',
            'treatment_plan.frequency' => 'nullable|string|max:255',
            'treatment_plan.observations' => 'nullable|string|max:1000',
            'photo_front' => 'nullable|url|max:5000',
            'photo_profile_right' => 'nullable|url|max:5000',
            'photo_profile_left' => 'nullable|url|max:5000',
            'consent_accepted' => 'nullable|boolean',
        ],
    ];
}
