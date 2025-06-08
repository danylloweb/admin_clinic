<?php

namespace App\DTO;

class VariantDTO
{
    public string $code;
    public string $label;
    public string $type;
    public bool   $scopable;
    public int    $sortOrder;
    public array  $group;
    public array  $values;

    public function __construct(
        string $code,
        string $label,
        string $type,
        bool   $scopable,
        int    $sortOrder,
        array  $group,
        array  $values
    ) {
        $this->code      = $code;
        $this->label     = $label;
        $this->type      = $type;
        $this->scopable  = $scopable;
        $this->sortOrder = $sortOrder;
        $this->group     = $group;
        $this->values    = $values;
    }
}
