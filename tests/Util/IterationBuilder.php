<?php

namespace PhpBench\Tests\Util;

use PhpBench\Model\Iteration;
use PhpBench\Model\Result\TimeResult;
use PhpBench\Model\ResultInterface;
use PhpBench\Model\Variant;

class IterationBuilder
{
    /**
     * @var VariantBuilder
     */
    private $variant;

    /**
     * @var ResultInterface[]
     */
    private $results = [];

    public function __construct(VariantBuilder $variant)
    {
        $this->variant = $variant;
    }

    public function setResult(TimeResult $timeResult): self
    {
        $this->results[] = $timeResult;

        return $this;
    }

    public function end(): VariantBuilder
    {
        return $this->variant;
    }

    public function build(Variant $variant): Iteration
    {
        return $variant->createIteration($this->results);
    }
}
