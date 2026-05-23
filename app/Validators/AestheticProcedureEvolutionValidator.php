<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

/**
 * Class AestheticProcedureEvolutionValidator.
 *
 * @package namespace App\Validators;
 */
class AestheticProcedureEvolutionValidator extends LaravelValidator
{
    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'schedule_id' => 'required|integer|exists:schedules,id|unique:aesthetic_procedure_evolutions,schedule_id',
            'patient_id' => 'required|integer|exists:patients,id',
            'professional_id' => 'required|integer|exists:users,id',
            'procedure_name' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'evolution_sessions' => 'nullable|array',
            'evolution_sessions.*.session_number' => 'nullable|integer|min:1',
            'evolution_sessions.*.date' => 'nullable|date',
            'evolution_sessions.*.procedure_performed' => 'nullable|string|max:255',
            'evolution_sessions.*.equipment_used' => 'nullable|string|max:255',
            'evolution_sessions.*.parameters_used' => 'nullable|string|max:2000',
            'evolution_sessions.*.products_used' => 'nullable|string|max:2000',
            'evolution_sessions.*.patient_reaction' => 'nullable|string|max:2000',
            'evolution_sessions.*.observations' => 'nullable|string|max:2000',
            'photo_before' => 'nullable|string',
            'photo_after' => 'nullable|string',
            'result_evaluation' => 'nullable|in:Excelente,Bom,Regular,Insatisfatorio',
            'patient_signature' => 'nullable|string',
            'professional_signature' => 'nullable|string',
            'signed_at' => 'nullable|date',
        ],
        ValidatorInterface::RULE_UPDATE => [
            'schedule_id' => 'sometimes|integer|exists:schedules,id',
            'patient_id' => 'sometimes|integer|exists:patients,id',
            'professional_id' => 'sometimes|integer|exists:users,id',
            'procedure_name' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'evolution_sessions' => 'nullable|array',
            'evolution_sessions.*.session_number' => 'nullable|integer|min:1',
            'evolution_sessions.*.date' => 'nullable|date',
            'evolution_sessions.*.procedure_performed' => 'nullable|string|max:255',
            'evolution_sessions.*.equipment_used' => 'nullable|string|max:255',
            'evolution_sessions.*.parameters_used' => 'nullable|string|max:2000',
            'evolution_sessions.*.products_used' => 'nullable|string|max:2000',
            'evolution_sessions.*.patient_reaction' => 'nullable|string|max:2000',
            'evolution_sessions.*.observations' => 'nullable|string|max:2000',
            'photo_before' => 'nullable|string',
            'photo_after' => 'nullable|string',
            'result_evaluation' => 'nullable|in:Excelente,Bom,Regular,Insatisfatorio',
            'patient_signature' => 'nullable|string',
            'professional_signature' => 'nullable|string',
            'signed_at' => 'nullable|date',
        ],
    ];
}
