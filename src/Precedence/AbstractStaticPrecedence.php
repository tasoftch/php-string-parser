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


use TASoft\Parser\Token\TokenInterface;

abstract class AbstractStaticPrecedence extends AbstractPrecedence
{
    /** @var array */
    protected $precedences;
    /** @var array */
    protected $associativities;


    public function getOperatorPrecedence(TokenInterface $operator, int &$precedence, int &$associativity): bool
    {
        if($this->precedences === NULL) $this->load();
        $precedence = $this->precedences[ $operator->getCode() ] ?? $this->precedences[ $operator->getContent() ] ?? NULL;
        if($precedence === NULL)
            return false;

        $associativity = $this->associativities[ $operator->getCode() ] ?? $this->associativities[ $operator->getContent() ] ?? NULL;
        if($associativity === NULL)
            return false;
        return true;
    }

    public static function merged(AbstractStaticPrecedence ...$precedences): self {
        $prec = new class extends AbstractStaticPrecedence {
            protected function load() {}
        };

        foreach($precedences as $precedence) {
            $prec->append($precedence);
        }
        return $prec;
    }

    public function append(AbstractStaticPrecedence $precedence): self {
        if($precedence->precedences === NULL) {
            $precedence->load();
        }
        foreach($precedence->precedences as $name => $p)
            $this->precedences[$name] = $p;
        foreach($precedence->associativities as $name => $p)
            $this->associativities[$name] = $p;
        return $this;
    }

    abstract protected function load();
}