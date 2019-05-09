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

namespace TASoft\Parser;


use TASoft\Parser\Exception\ParserAbortException;
use TASoft\Parser\Token\TokenInterface;
use TASoft\Parser\TokenSet\TokenSetInterface;

abstract class AbstractExpressionParser extends AbstractParser
{
    private $parentheses = [];
    private $currentParentheseCounterPart;

    private $checkExpected = true;

    /** @var TokenSetInterface|null */
    private $operandTokenSet;
    /** @var TokenSetInterface|null */
    private $operatorTokenSet;

    private $expectsOperator = false;

    /**
     * @return bool
     */
    public function getExpectsOperator()
    {
        return $this->expectsOperator;
    }


    /**
     * @return null|TokenSetInterface
     */
    public function getOperandTokenSet(): ?TokenSetInterface
    {
        return $this->operandTokenSet;
    }

    /**
     * @param null|TokenSetInterface $operandTokenSet
     */
    public function setOperandTokenSet(?TokenSetInterface $operandTokenSet): void
    {
        $this->operandTokenSet = $operandTokenSet;
    }

    /**
     * @return null|TokenSetInterface
     */
    public function getOperatorTokenSet(): ?TokenSetInterface
    {
        return $this->operatorTokenSet;
    }

    /**
     * @param null|TokenSetInterface $operatorTokenSet
     */
    public function setOperatorTokenSet(?TokenSetInterface $operatorTokenSet): void
    {
        $this->operatorTokenSet = $operatorTokenSet;
    }

    /**
     * @return bool
     */
    public function checkExpected(): bool
    {
        return $this->checkExpected;
    }

    /**
     * @param bool $checkExpected
     */
    public function setCheckExpected(bool $checkExpected): void
    {
        $this->checkExpected = $checkExpected;
    }

    protected function parserDidStart()
    {
        parent::parserDidStart();
        $this->setNextExpectedOperand();
    }


    protected function parseToken(TokenInterface $token, int $options)
    {
        $counterPart = NULL;
        if($this->isOpeningParenthese($token, $counterPart)) {
            if(!is_callable($counterPart))
                throw new ParserAbortException("Opening parenthese without counter part is invalid");

            $this->parentheses[] = [$token, $counterPart];
            $this->currentParentheseCounterPart = $counterPart;
            $this->parseOpenParenthese($token);

            $this->setNextExpectedOperand();
            return;
        }

        if(NULL !== $cp = $this->currentParentheseCounterPart) {
            if($cp($token)) {
                $opening = array_pop($this->parentheses);
                $this->currentParentheseCounterPart = end($this->parentheses)[1] ?? NULL;
                $this->parseCloseParenthese($token, $opening[0]);

                $this->setNextExpectedOperator();
                return;
            }
        }

        if($this->isEndOfExpression($token)) {
            $this->parseEndOfExpression($token);
            return;
        }

        if($this->isOperand($token)) {
            $this->setNextExpectedOperator();
            $this->parseOperand($token);
            return;
        }

        if($this->isOperator($token)) {
            $this->setNextExpectedOperand();
            $this->parseOperator($token);
            return;
        }
    }


    protected function setNextExpectedOperator() {
        if($this->checkExpected()) {
            $this->expectsOperator = true;
            $this->setNextExpected(function($token) {
                if($this->isOperator($token))
                    return true;
                if($this->isEndOfExpression($token))
                    return true;

                // But accepts also a closing parenthese counter part.
                if(NULL !== $cb = $this->currentParentheseCounterPart AND $cb($token))
                    return true;
                return false;
            });
        }
    }

    protected function setNextExpectedOperand() {
        if($this->checkExpected()) {
            $this->expectsOperator = false;
            $this->setNextExpected(function($token) {
                if($this->isOperand($token))
                    return true;
                $null = NULL;
                if($this->isOpeningParenthese($token, $null))
                    return true;
                if(NULL !== $cb = $this->currentParentheseCounterPart AND $cb($token))
                    return true;
                return false;
            });
        }
    }


    /**
     * Parse token as opening parenthese
     *
     * @param TokenInterface $token
     * @return void
     */
    abstract protected function parseOpenParenthese(TokenInterface $token);

    /**
     * Parse token as closing parenthese
     *
     * @param TokenInterface $token
     * @param TokenInterface $openingToken
     * @return void
     */
    abstract protected function parseCloseParenthese(TokenInterface $token, TokenInterface $openingToken);

    /**
     * Parse token as operator
     *
     * @param TokenInterface $token
     * @return void
     */
    abstract protected function parseOperator(TokenInterface $token);

    /**
     * Parse token as operand
     *
     * @param TokenInterface $token
     * @return void
     */
    abstract protected function parseOperand(TokenInterface $token);

    /**
     * Finalize expression and prepare for a new one.
     *
     * @param TokenInterface $token
     * @return void
     */
    abstract protected function parseEndOfExpression(TokenInterface $token);

    /**
     * Decide whether a token is an operand
     *
     * @param TokenInterface $token
     * @return bool
     */
    protected function isOperand(TokenInterface $token): bool {
        return ($set = $this->getOperandTokenSet()) ? $set->tokenIsMember($token) : false;
    }

    /**
     * Decide whether a token is an operator
     *
     * @param TokenInterface $token
     * @return bool
     */
    protected function isOperator(TokenInterface $token): bool {
        return ($set = $this->getOperatorTokenSet()) ? $set->tokenIsMember($token) : false;
    }

    /**
     * Decide whether a token is an opening parenthese.
     * On returning TRUE, you MUST specify a counterPart (closing parenthese)
     *
     * @param TokenInterface $token
     * @param callable|null $counterPart    callback to check, if the token closes the parenthese
     * @return bool
     */
    protected function isOpeningParenthese(TokenInterface $token, ?callable &$counterPart): bool {
        if($token->getContent() == '(') {
            $counterPart = function(TokenInterface $token) {
                return $token->getContent() == ')';
            };
            return true;
        }
        return false;
    }

    protected function isEndOfExpression(TokenInterface $token): bool {
        if($token->getContent() == ';')
            return true;
        return false;
    }
}