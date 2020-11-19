<?php declare(strict_types=1);
/**
 * Tested with phpunit-9.4.3.phar & PHP 7.4
 */

require(__DIR__.'/../vendor/autoload.php');

use PHPUnit\Framework\TestCase;
use resist\Matex\Evaluator;
use resist\Matex\Exception\MatexException;

final class EvaluatorTest extends TestCase
{

    /**
     * @dataProvider getExpressions
     * @throws MatexException
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
            ['1 + 2', 3], // Example from original documentation #1
            ['1 + 1', 2],
            ['1 + 0.11', 1.11],
            ['2*2', 4],
            ['11/2', 5.5],
            ['1 - 1', 0],
            ['1-1', 0],
            [' 1-1 ', 0],
            ['1 + 2 - 3 * 2 / 2', 0],
            ['6 / (1 + 2)', 2],
            [' 0', 0],
            ['.1', 0.1],
            ['-1', -1],
            ['-1-10', -11],
            ['-2 + 2 * -125 / 5', -52],
        ];
    }

    /**
     * Example from original documentation #2
     * @throws MatexException
     */
    public function testConcatenation(): void
    {
        $evaluator = new Evaluator();
        self::assertEquals(
            'String concatenation',
            $evaluator->execute('"String" + " " + "concatenation"')
        );
    }

    /**
     * @dataProvider getExpressionsWithVariables
     * @throws MatexException
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
            ['x+y', ['x' => 1, 'y' => 2], 3], // Example from original documentation #3
            ['x+1', ['x' => 1], 2],
        ];
    }

    /**
     * Example from original documentation #4
     * @throws MatexException
     */
    public function testDynamicVariables(): void
    {
        $evaluator = new Evaluator();
        $evaluator->variables = ['a' => 1];
        $evaluator->onVariable = [$this, 'doVariable'];
        self::assertEquals(
            5,
            $evaluator->execute('a+b')
        );
    }

    /**
     * Example from original documentation #4 - helper function
     */
    public function doVariable($name, &$value): void
    {
        switch ($name) {
            case 'b':
                $value = 4;
                break;
        }
    }

    /**
     * Example from original documentation #5
     * @throws MatexException
     */
    public function testFunctions(): void
    {
        $evaluator = new Evaluator();
        $evaluator->functions = [
            'sum' => ['ref' => 'EvaluatorTest::sum', 'arc' => null],
            'min' => ['ref' => 'min', 'arc' => null],
        ];
        self::assertEquals(
            5,
            $evaluator->execute('sum(1, 2, 3) + min(0, -1, 4)')
        );
    }

    public static function sum(...$arguments) {
        return array_sum($arguments);
    }

    public function testExceptions(): void
    {
        $evaluator = new Evaluator();
        $this->expectException(MatexException::class);
        $evaluator->execute('1/0');
    }

}
