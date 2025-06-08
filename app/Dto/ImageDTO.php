<?php

namespace App\DTO;

class ImageDTO
{
    public string $value;
    public string $type;
    public string $alt;

    public function __construct(string $value, string $type, string $alt)
    {
        $this->value = $value;
        $this->type  = $type;
        $this->alt   = $alt;
    }
}
