<?php declare(strict_types=1);
/**
 * Tested with phpunit-9.4.3.phar & PHP 7.4
 */

require(__DIR__.'/../vendor/autoload.php');

use PHPUnit\Framework\TestCase;
use resist\Matex\Evaluator;

final class EvaluatorTest extends TestCase
{

    /**
     * @dataProvider getExpressions
     * @throws \resist\Matex\Exception
     */
    public function testExpressions(string $expression, float $expected): void
    {
        $evaluator = new Evaluator();
        self::assertEquals(
            $expected,
            $evaluator->execute($expression)
        );
    }

    public function getExpressions(): array
    {
        return [
            ['1 + 1', 2],
            ['1 + 0.11', 1.11],
            ['2*2', 4],
            ['11/2', 5.5],
            ['1 - 1', 0],
            ['1-1', 0],
            [' 1-1 ', 0],
            ['1 + 2 - 3 * 2 / 2', 0],
            ['6 / (1 + 2)', 2],
        ];
    }

    /**
     * @dataProvider getExpressionsWithVariables
     * @throws \resist\Matex\Exception
     */
    public function testVariables(string $expression, array $variables, float $expected): void
    {
        $evaluator = new Evaluator();
        $evaluator->variables = $variables;
        self::assertEquals(
            $expected,
            $evaluator->execute($expression)
        );
    }

    public function getExpressionsWithVariables(): array
    {
        return [
            ['x+y', ['x' => 1, 'y' => 2], 3],
            ['x+1', ['x' => 1], 2],
        ];
    }
}
