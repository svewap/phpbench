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

namespace PhpBench\Tests\System\benchmarks\set5;

class AssertFailBench
{
    /**
     * @Assert("mean(variant.time.net) < 1 microsecond")
     */
    public function benchFail(): void
    {
        usleep(10);
    }
}
