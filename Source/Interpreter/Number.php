<?php

namespace Source\Interpreter;

class Number implements IInterpreter
{
    public $digit;

    public function __construct(float $number)
    {
        $this->digit = $number; 
    }

    public function interpret()
    {
        return (float)$this->digit;
    }
}
