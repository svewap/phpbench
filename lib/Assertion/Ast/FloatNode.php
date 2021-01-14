<?php

namespace PhpBench\Assertion\Ast;

class FloatNode implements NumberNode
{
    /**
     * @var float
     */
    private $number;

    public function __construct(float $number)
    {
        $this->number = $number;
    }

    public function getNumber(): float
    {
        return $this->number;
    }
}
