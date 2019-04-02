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


use TASoft\Parser\Exception\MissmatchingParentheseException;
use TASoft\Parser\Exception\ParserException;
use TASoft\Parser\Precedence\PrecedenceInterface;
use TASoft\Parser\Token\TokenInterface;

abstract class AbstractExpressionCompiler extends AbstractExpressionParser
{
    const USE_LEFT_OPERAND = 1;
    const USE_RIGHT_OPERAND = 2;
    const USE_BOTH_OPERANDS = 3;

    /** @var PrecedenceInterface*/
    private $precedence;

    /** @var array Stacks operands to order them against precedence */
    private $operand_queue;
    /** @var array Stacks operators: stack frame: [token, isParenthese, isOperation] */
    private $operator_queue;

    /**
     * @inheritdoc
     */
    protected function parserDidStart()
    {
        parent::parserDidStart();
        $this->operand_queue = $this->operator_queue = [];
    }

    /**
     * If there are still operations in stack, compile them before finishing.
     *
     * @param array $errors
     * @return bool
     */
    protected function parserWillFinish(array $errors)
    {
        if(count($this->operand_queue) + count($this->operator_queue))
            $this->_terminate(NULL);
        return parent::parserWillFinish($errors);
    }

    /**
     * @return PrecedenceInterface
     */
    public function getPrecedence(): PrecedenceInterface
    {
        return $this->precedence;
    }

    /**
     * @param PrecedenceInterface $precedence
     */
    public function setPrecedence(PrecedenceInterface $precedence): void
    {
        $this->precedence = $precedence;
    }


    /**
     * Private method to push operators and maintain data structure
     *
     * @param $token
     * @param bool $isParenthese
     * @param bool $isOperation
     */
    private function _pushOperator($token, bool $isParenthese = false, bool $isOperation = false) {
        $this->operator_queue[] = [$token, $isParenthese, $isOperation];
    }

    /**
     * Obtain last operator in stack or NULL
     *
     * @param null|TokenInterface $token
     * @param bool|null $isParenthese
     * @param bool|null $isOperation
     * @return bool
     */
    private function _lastOperator(?TokenInterface &$token, ?bool &$isParenthese, ?bool &$isOperation = false): bool {
        list($token, $isParenthese, $isOperation) = end($this->operator_queue);
        return $this->operator_queue ? true : false;
    }


    /**
     * @inheritdoc
     */
    protected function parseOpenParenthese(TokenInterface $token)
    {
        $this->_pushOperator($token, true, false);
    }

    /**
     * @inheritdoc
     */
    protected function parseCloseParenthese(TokenInterface $token, TokenInterface $openingToken)
    {
        while ($this->_lastOperator($lastToken, $parenthese)) {
            if($parenthese) {
                array_pop($this->operator_queue);
                break;
            } else {
                $this->compileLastOperationInStack();

                if(!$this->operator_queue) {
                    $e = new MissmatchingParentheseException("Missing open parenthese for %s", $token->getContent());
                    $e->setToken($token);
                    throw $e;
                }
            }
        }
    }

    /**
     * If the operator has a lower precedence than the last in stack, compile it.
     *
     * @inheritdoc
     */
    protected function parseOperator(TokenInterface $token)
    {
        $pre = $this->getPrecedence();

        while ($this->_lastOperator($last, $parenthese)) {
            if($parenthese)
                break;

            if($pre->compareOperators($last, $token) > PrecedenceInterface::PRECEDENCE_EQUAL)
                break;

            $this->compileLastOperationInStack();
        }

        $this->_pushOperator($token, false, true);
    }

    /**
     * @inheritdoc
     */
    protected function parseOperand(TokenInterface $token)
    {
        $this->operand_queue[] = $token;
    }

    /**
     * @inheritdoc
     */
    protected function parseEndOfExpression(TokenInterface $token)
    {
        $this->_terminate($token);
    }

    /**
     * Intern method to terminate an expression.
     *
     * @internal
     */
    private function _terminate(?TokenInterface $token) {
        while($this->operator_queue) {
            $this->compileLastOperationInStack();
        }

        $final = array_pop($this->operand_queue);
        if($d = count($this->operand_queue)) {
            throw new ParserException("Parsing failed because there are still %d object(s) in stack", 0, NULL, $d);
        }

        $this->compileExpression($final, $token);
        $this->operand_queue = $this->operator_queue = [];
    }

    /**
     * Take last operator and last two operands and compile them.
     */
    protected function compileLastOperationInStack() {
        /** @var TokenInterface $token */
        list($token, $isParenthese, $isOperation) = array_pop($this->operator_queue);

        if($isParenthese) {
            $e = new MissmatchingParentheseException("Missing close parenthese for %s", $token->getContent());
            throw $e;
        }

        if($isOperation) {
            $right = array_pop($this->operand_queue);
            $left = array_pop($this->operand_queue);

            $res = $this->compileOperation($token, $left, $right);
            if($res & self::USE_LEFT_OPERAND)
                $this->operand_queue[] = $left;
            if($res & self::USE_RIGHT_OPERAND)
                $this->operand_queue[] = $right;
        } else {
            $this->compileNonOperation($token, $isParenthese);
        }
    }

    /**
     * Trying to compile an operator that is not representing an operation will fall back to this method.
     *
     * @param TokenInterface $token
     * @param bool $isParenthese
     */
    protected function compileNonOperation(TokenInterface $token, bool $isParenthese) {
    }

    /**
     * Joins two operand with an operation.
     * This method should return a bitmask using the two class constants self::USE_*_OPERAND or null.
     * The left and right operands are popped from stack if the return constant values do not require them in the stack.
     * So normally you create the operation, set the new stack value to left or right operand and declare in the return value which one should be pushed into the stack.
     * @example left operand: [T_LNUMBER, 56], right operand [T_LNUMBER, 18] $code: 0, $content = + ::> $leftOperand = <created-operation>; return self::USE_LEFT_OPERAND;
     * @example Negate operation (only one operand): $rightOperand = <negate> $rightOperand; return self::USE_LEFT_OPERAND | self::USE_RIGHT_OPERAND;
     *
     * @param TokenInterface $operator
     * @param $leftOperand
     * @param $rightOperand
     * @return int
     * @see AbstractExpressionCompiler::USE_* constants
     */
    abstract protected function compileOperation(TokenInterface $operator, &$leftOperand, &$rightOperand): int;

    /**
     * Called when the expression did end.
     * If $token is NULL, the whole parsing process is terminating.
     *
     * @param $finalOperand
     * @param TokenInterface|null $token
     * @return mixed
     */
    abstract protected function compileExpression($finalOperand, ?TokenInterface $token);
}