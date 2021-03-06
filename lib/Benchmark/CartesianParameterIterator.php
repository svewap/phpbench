<?php

/*
 * This file is part of the PHPBench package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace PhpBench\Benchmark;

use PhpBench\Model\ParameterSet;

class CartesianParameterIterator implements \Iterator
{
    private $sets = [];
    private $index = 0;
    private $max;
    private $current = [];
    private $break = false;

    /**
     * @var string
     */
    private $key;

    public function __construct(array $parameterSets)
    {
        foreach ($parameterSets as $parameterSet) {
            $this->sets[] = new \ArrayIterator($parameterSet);
        }

        if (empty($parameterSets)) {
            $this->break = true;
        }

        $this->max = count($parameterSets) - 1;
    }

    public function current()
    {
        return $this->getParameterSet();
    }

    public function next(): void
    {
        for ($index = 0; $index <= $this->max; $index++) {
            $this->sets[$index]->next();

            if (true === $this->sets[$index]->valid()) {
                break;
            }

            $this->sets[$index]->rewind();

            if ($index === $this->max) {
                $this->break = true;

                break;
            }
        }

        $this->index++;
        $this->update();
    }

    public function key(): string
    {
        return $this->key;
    }

    public function rewind(): void
    {
        $this->index = 0;

        foreach ($this->sets as $set) {
            $set->rewind();
        }
        $this->update();
    }

    public function valid(): bool
    {
        return false === $this->break;
    }

    private function update(): void
    {
        $this->current = [];
        $key = [];

        foreach ($this->sets as $set) {
            $this->current = array_merge(
                $this->current,
                $set->current() ?: []
            );
            $key[] = $set->key();
        }
        $this->key = implode(',', $key);
    }

    private function getParameterSet(): ParameterSet
    {
        return new ParameterSet($this->key, $this->current);
    }
}
