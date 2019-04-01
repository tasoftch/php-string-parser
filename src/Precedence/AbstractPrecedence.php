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

namespace TASoft\Parser\Precedence;


use TASoft\Parser\Exception\UndefinedPrecedenceException;
use TASoft\Parser\Token\TokenInterface;

abstract class AbstractPrecedence implements PrecedenceInterface
{
    const PREC_LOWEST = 0;
    const PREC_X_LOW = 50;
    const PREC_VERY_LOW = 100;
    const PREC_LOW = 500;

    const PREC_X_MEDIUM = 1000;
    const PREC_MEDIUM = 5000;

    const PREC_HIGH = 10000;
    const PREC_VERY_HIGH = 20000;
    const PREC_X_HIGH = 400000;
    const PREC_HIGHEST = 65535;

    const ASSOC_LEFT = -1;
    const ASSOC_RIGHT = 1;
    const ASSOC_NONE = 0;


    public function compareOperators(TokenInterface $leftOperator, TokenInterface $rightOperator): int
    {
        return
            $this->_getOperatorPrecedence($leftOperator)
            <=>
            $this->_getOperatorPrecedence($rightOperator)
        ;
    }

    abstract public function getOperatorPrecedence(TokenInterface $operator, int &$precedence, int &$associativity): bool;

    private function _getOperatorPrecedence(TokenInterface $operator): int {
        if(!$this->getOperatorPrecedence($operator, $pre = 0, $ass = 0)) {
            $e = new UndefinedPrecedenceException("No precedence defined for operation %s", 0, NULL, $operator->getContent());
            $e->setToken($operator);
            throw $e;
        }

        // Dynamic calculation that equal precedences with left associativity have a higher precedence
        return (65535 * 3 + 3) - ($pre * 3 + $ass + 1);
    }
}