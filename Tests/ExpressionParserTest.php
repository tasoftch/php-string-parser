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
 * ExpressionParserTest.php
 * php-parser
 *
 * Created on 31.03.19 21:43 by thomas
 */

use PHPUnit\Framework\TestCase;
use TASoft\Parser\AbstractExpressionParser;
use TASoft\Parser\Exception\UnexpectedTokenException;
use TASoft\Parser\Token\TokenInterface;
use TASoft\Parser\TokenSet\IrelevantTokenSet;
use TASoft\Parser\TokenSet\MathematicalOperatorSet;
use TASoft\Parser\TokenSet\NumberOperandSet;
use TASoft\Parser\TokenSet\TokenSetInterface;

class ExpressionParserTest extends TestCase
{
    public function testParser() {
        $parser = new MockParser();
        $parser->parseString('1 + 3 * /* HAHA */ 5');

        $this->assertEquals("1+3*5", $parser->content);
    }

    /**
     * @expectedException TASoft\Parser\Exception\UnexpectedTokenException
     */
    public function testParserInvalidOperand() {
        $parser = new MockParser();
		$this->expectException(UnexpectedTokenException::class);
        $parser->parseString('1 + 3  5');
    }

    /**
     * @expectedException TASoft\Parser\Exception\UnexpectedTokenException
     */
    public function testParserInvalidOperator() {
        $parser = new MockParser();
		$this->expectException(UnexpectedTokenException::class);
        $parser->parseString('1 + 3 + / 5');
    }
}

class MockParser extends AbstractExpressionParser {
    public $content = "";
    protected function parseOpenParenthese(TokenInterface $token)
    {
        $this->parseOperator($token);
    }

    protected function parseCloseParenthese(TokenInterface $token, TokenInterface $openingToken)
    {
        $this->parseOperator($token);
    }

    protected function parseOperator(TokenInterface $token)
    {
        $this->content .= $token->getContent();
    }

    protected function parseOperand(TokenInterface $token)
    {
        $this->parseOperator($token);
    }

    protected function parseEndOfExpression(TokenInterface $token)
    {
        $this->parseOperator($token);
    }

    public function getOperandTokenSet(): ?TokenSetInterface
    {
        return new NumberOperandSet();
    }

    public function getOperatorTokenSet(): ?TokenSetInterface
    {
        return new MathematicalOperatorSet();
    }

    public function getIgnoredTokenSet(): ?TokenSetInterface
    {
        return new IrelevantTokenSet();
    }
}
