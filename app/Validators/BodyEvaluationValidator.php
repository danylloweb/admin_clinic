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
            'patient_id' => 'required|integer|exists:patients,id',
            'professional_id' => 'required|integer|exists:users,id',
            'weight' => 'nullable|numeric|min:30|max:300',
            'height' => 'nullable|numeric|min:100|max:250',
            'fat_percentage' => 'nullable|numeric|min:0|max:100',
            'muscle_mass' => 'nullable|numeric|min:0|max:100',
            'objectives' => 'nullable|array',
            'objectives.*' => 'string|max:100',
            'perimetry' => 'nullable|array',
            'cellulite' => 'nullable|array',
            'flaccidity' => 'nullable|array',
            'liquid_retention' => 'nullable|boolean',
            'body_map_areas' => 'nullable|array',
            'body_map_areas.*' => 'string|max:100',
            'medical_history' => 'nullable|array',
            'medical_history.*' => 'string|max:100',
            'previous_procedures' => 'nullable|string|max:2000',
            'treatment_plan' => 'nullable|array',
            'treatment_plan.*' => 'string|max:255',
            'evolution_sessions' => 'nullable|array',
            'photo_front' => 'nullable|url|max:2048',
            'photo_profile_right' => 'nullable|url|max:2048',
            'photo_profile_left' => 'nullable|url|max:2048',
            'consent_accepted' => 'required|boolean|in:1,true',
        ],
        ValidatorInterface::RULE_UPDATE => [
            'patient_id' => 'sometimes|integer|exists:patients,id',
            'professional_id' => 'nullable|integer|exists:users,id',
            'weight' => 'nullable|numeric|min:30|max:300',
            'height' => 'nullable|numeric|min:100|max:250',
            'fat_percentage' => 'nullable|numeric|min:0|max:100',
            'muscle_mass' => 'nullable|numeric|min:0|max:100',
            'objectives' => 'nullable|array',
            'objectives.*' => 'string|max:100',
            'perimetry' => 'nullable|array',
            'cellulite' => 'nullable|array',
            'flaccidity' => 'nullable|array',
            'liquid_retention' => 'nullable|boolean',
            'body_map_areas' => 'nullable|array',
            'body_map_areas.*' => 'string|max:100',
            'medical_history' => 'nullable|array',
            'medical_history.*' => 'string|max:100',
            'previous_procedures' => 'nullable|string|max:2000',
            'treatment_plan' => 'nullable|array',
            'treatment_plan.*' => 'string|max:255',
            'evolution_sessions' => 'nullable|array',
            'photo_front' => 'nullable|url|max:2048',
            'photo_profile_right' => 'nullable|url|max:2048',
            'photo_profile_left' => 'nullable|url|max:2048',
            'consent_accepted' => 'nullable|boolean',
        ],
    ];
}


