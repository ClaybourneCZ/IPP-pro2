<?php

namespace IPP\Student;

class VarLit 
{
    public string $Name;
    public ?string $Type;
    public ?string $Where;
    public bool $Defined;
    public ?bool $Inited;
    public int|string|bool|float $Value;
}