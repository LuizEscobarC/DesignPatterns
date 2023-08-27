<?php

namespace Source\Interpreter;

class Subtraction implements IInterpreter
{
    public $leftNumber;
    public $rightNumber;


    public function __construct(IInterpreter $left, IInterpreter $right)
    {
        $this->leftNumber = $left;
        $this->rightNumber = $right; 
    }

    public function interpret(): float
    {
        return $this->leftNumber->interpret() - $this->rightNumber->interpret();
    }
}
