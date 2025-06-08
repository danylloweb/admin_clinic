<?php

namespace App\DTO;

class OtherAttributeDTO
{
    public string $name;
    public string $value;
    public string $type;

    public function __construct(string $name, string $value, string $type)
    {
        $this->name  = $name;
        $this->value = $value;
        $this->type  = $type;
    }
}
