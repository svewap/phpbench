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

namespace PhpBench\Assertion;

use Exception;
use PhpBench\Assertion\Ast\Comparison;
use PhpBench\Assertion\Exception\ExpressionError;
use PhpBench\Assertion\Exception\ExpressionEvaluatorError;
use PhpBench\Assertion\Exception\PropertyAccessError;
use PhpBench\Model\Variant;
use RuntimeException;

class AssertionProcessor
{
    /**
     * @var ExpressionEvaluatorFactory
     */
    private $evaluator;

    /**
     * @var ExpressionParser
     */
    private $parser;

    /**
     * @var ExpressionPrinterFactory
     */
    private $printer;

    public function __construct(
        ExpressionParser $parser,
        ExpressionEvaluatorFactory $evaluator,
        ExpressionPrinterFactory $printer
    ) {
        $this->evaluator = $evaluator;
        $this->parser = $parser;
        $this->printer = $printer;
    }

    public function assert(Variant $variant, string $assertion): AssertionResult
    {
        $node = $this->parser->parse($assertion);

        if (!$node instanceof Comparison) {
            throw new ExpressionEvaluatorError(sprintf(
                'Assertion must be a comparison, got "%s"',
                get_class($node)
            ));
        }

        $args = (function (array $variantData) use ($variant) {
            return [
                'variant' => $variantData,
                'baseline' => $variant->getBaseline() ? $this->buildVariantData($variant->getBaseline()) : $variantData,
            ];
        })($this->buildVariantData($variant));

        try {
            $result = $this->evaluator->createWithParameters($args)->evaluate($node);
        } catch (PropertyAccessError $error) {
            return AssertionResult::warning(ExpressionError::forExpression($assertion, $error->getMessage())->getMessage());
        } catch (Exception $error) {
            throw ExpressionError::forExpression($assertion, $error->getMessage());
        }
        $printer = $this->printer->create($args);

        if (!$result instanceof ComparisonResult) {
            throw new RuntimeException(sprintf(
                'Expected comparison result, got "%s"',
                gettype($result)
            ));
        }

        if ($result->isTolerated()) {
            return AssertionResult::tolerated($printer->format($node));
        }

        if ($result->isTrue()) {
            return AssertionResult::ok();
        }

        return AssertionResult::fail($printer->format($node));
    }

    /**
     * @return parameters
     */
    private function buildVariantData(Variant $variant): array
    {
        return $variant->getAllMetricValues();
    }
}
