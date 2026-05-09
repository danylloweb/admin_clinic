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
        ],
        ValidatorInterface::RULE_UPDATE => [
            'patient_id' => 'sometimes|exists:patients,id',
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
        ],
    ];
}
