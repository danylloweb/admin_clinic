<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

/**
 * Class BodyEvaluationValidator.
 *
 * @package namespace App\Validators;
 */
class BodyEvaluationValidator extends LaravelValidator
{
    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'patient_id' => 'required|exists:patients,id',
            'professional_id' => 'nullable|exists:users,id',
            'weight' => 'nullable|numeric|min:30|max:300',
            'height' => 'nullable|numeric|min:100|max:250',
            'fat_percentage' => 'nullable|numeric|min:0|max:100',
            'muscle_mass' => 'nullable|numeric|min:0|max:100',
            'objectives' => 'nullable|array',
            'perimetry' => 'nullable|array',
            'cellulite' => 'nullable|array',
            'flaccidity' => 'nullable|array',
            'liquid_retention' => 'nullable|boolean',
            'body_map_areas' => 'nullable|array',
            'medical_history' => 'nullable|array',
            'previous_procedures' => 'nullable|string|max:2000',
            'treatment_plan' => 'nullable|array',
            'evolution_sessions' => 'nullable|array',
            'photo_front' => 'nullable|url|max:2048',
            'photo_profile_right' => 'nullable|url|max:2048',
            'photo_profile_left' => 'nullable|url|max:2048',
            'consent_accepted' => 'nullable|boolean',
        ],
        ValidatorInterface::RULE_UPDATE => [
            'patient_id' => 'sometimes|exists:patients,id',
            'professional_id' => 'nullable|exists:users,id',
            'weight' => 'nullable|numeric|min:30|max:300',
            'height' => 'nullable|numeric|min:100|max:250',
            'fat_percentage' => 'nullable|numeric|min:0|max:100',
            'muscle_mass' => 'nullable|numeric|min:0|max:100',
            'objectives' => 'nullable|array',
            'perimetry' => 'nullable|array',
            'cellulite' => 'nullable|array',
            'flaccidity' => 'nullable|array',
            'liquid_retention' => 'nullable|boolean',
            'body_map_areas' => 'nullable|array',
            'medical_history' => 'nullable|array',
            'previous_procedures' => 'nullable|string|max:2000',
            'treatment_plan' => 'nullable|array',
            'evolution_sessions' => 'nullable|array',
            'photo_front' => 'nullable|url|max:2048',
            'photo_profile_right' => 'nullable|url|max:2048',
            'photo_profile_left' => 'nullable|url|max:2048',
            'consent_accepted' => 'nullable|boolean',
        ],
    ];
}


