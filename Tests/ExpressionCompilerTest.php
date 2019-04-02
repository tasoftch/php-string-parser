<?php
/**
 * Copyright (c) 2019 TASoft Applications, Th. Abplanalp <info@tasoft.ch>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

/**
 * PrecedenceExpressionTest.php
 * php-parser
 *
 * Created on 01.04.19 20:27 by thomas
 */

use PHPUnit\Framework\TestCase;
use TASoft\Parser\AbstractExpressionCompiler;
use TASoft\Parser\Precedence\AbstractStaticPrecedence;
use TASoft\Parser\Precedence\AssignmentPrecedence;
use TASoft\Parser\Precedence\BitwisePrecedence;
use TASoft\Parser\Precedence\BooleanPrecedence;
use TASoft\Parser\Precedence\ComparisonPrecedence;
use TASoft\Parser\Precedence\LogicalPrecedence;
use TASoft\Parser\Precedence\MathematicalPrecedence;
use TASoft\Parser\Token\TokenInterface;
use TASoft\Parser\TokenSet\AssignmentOperatorSet;
use TASoft\Parser\TokenSet\BitwiseOperatorSet;
use TASoft\Parser\TokenSet\BooleanOperatorSet;
use TASoft\Parser\TokenSet\ComparisonOperatorSet;
use TASoft\Parser\TokenSet\IrelevantTokenSet;
use TASoft\Parser\TokenSet\LogicalOperatorSet;
use TASoft\Parser\TokenSet\MathematicalOperatorSet;
use TASoft\Parser\TokenSet\NameSpecifierOperandSet;
use TASoft\Parser\TokenSet\NumberOperandSet;
use TASoft\Parser\TokenSet\StringOperandSet;
use TASoft\Parser\TokenSet\StringOperatorSet;

class ExpressionCompilerTest extends TestCase
{
    private function createParser() {
        $parser = new class extends AbstractExpressionCompiler {
            public $operations = [];
            private $pointer = 1;

            protected function compileOperation(TokenInterface $operator, &$leftOperand, &$rightOperand): int
            {
                if($leftOperand instanceof TokenInterface)
                    $leftOperand = $leftOperand->getContent();
                if($rightOperand instanceof TokenInterface)
                    $rightOperand = $rightOperand->getContent();
                $p = $this->pointer++;

                $this->operations[] = "$leftOperand" . $operator->getContent() . "$rightOperand => ~$p";

                $leftOperand = "~$p";
                return AbstractExpressionCompiler::USE_LEFT_OPERAND;
            }

            protected function compileExpression($finalOperand, ?TokenInterface $token)
            {
                $this->operations[] = $finalOperand;
                $this->pointer = 1;
            }

            protected function parserDidStart()
            {
                parent::parserDidStart();
                $this->operations = [];
            }
        };

        $parser->setPrecedence(AbstractStaticPrecedence::merged(
            new MathematicalPrecedence(),
            new ComparisonPrecedence(),
            new LogicalPrecedence(),
            new BooleanPrecedence(),
            new BitwisePrecedence(),
            new AssignmentPrecedence()
        ));

        $parser->setOperatorTokenSet(
            (new AssignmentOperatorSet())
                ->append(new BitwiseOperatorSet())
            ->append(new BooleanOperatorSet())
            ->append(new ComparisonOperatorSet())
            ->append(new LogicalOperatorSet())
            ->append(new MathematicalOperatorSet())
            ->append(new StringOperatorSet())
        );

        $parser->setOperandTokenSet(
            (new NameSpecifierOperandSet())
            ->append(new NumberOperandSet())
            ->append(new StringOperandSet())
        );

        $parser->setIgnoredTokenSet(
            (new IrelevantTokenSet())
        );

        return $parser;
    }

    public function testSimpleExpressionParser() {
        $parser = $this->createParser();

        $parser->parseString("(2 + 5) * 7");
        $this->assertEquals([
            "2+5 => ~1",
            "~1*7 => ~2",
            "~2"
        ], $parser->operations);
    }

    public function testComplexExpression() {
        $parser = $this->createParser();

        $parser->parseString("2   + 7 ** ( 3 - 1) / (8 % 3 + 2) > 15 AND nor = FALSE");
        $this->assertEquals([
            0 => '3-1 => ~1',
            1 => '7**~1 => ~2',
            2 => '8%3 => ~3',
            3 => '~3+2 => ~4',
            4 => '~2/~4 => ~5',
            5 => '2+~5 => ~6',
            6 => '~6>15 => ~7',
            7 => 'nor=FALSE => ~8',
            8 => '~7AND~8 => ~9',
            9 => '~9'
        ], $parser->operations);
    }

    public function testPrecedence() {
        $parser = $this->createParser();
        $parser->parseString("My = TRUE && FALSE;");
        $this->assertEquals(array (
            0 => 'TRUE&&FALSE => ~1',
            1 => 'My=~1 => ~2',
            2 => '~2',
        ), $parser->operations);

        $parser->parseString("My = TRUE AND FALSE;");
        $this->assertEquals(array (
            0 => 'My=TRUE => ~1',
            1 => '~1ANDFALSE => ~2',
            2 => '~2',
        ), $parser->operations);
    }
}
