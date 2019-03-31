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

namespace TASoft\Parser\TokenSet;


use TASoft\Parser\Token\TokenInterface;

class TokenSet implements TokenSetInterface
{
    protected $tokens = [];
    public $caseInsensitive = true;

    public function tokenIsMember(TokenInterface $token): bool
    {
        if($this->tokens) {
            $comp = $this->caseInsensitive ? 'strcasecmp' : 'strcmp';

            foreach ($this->tokens as $expect) {
                $code = $expect;
                $value = $expect;
                repeat:
                if(is_callable($expect) && $expect($token))
                    return true;

                if(is_numeric($code) && $token->getCode() == $expect)
                    return true;

                if(is_string($value) && $comp($token->getContent(), $expect) == 0)
                    return true;

                if(is_array($expect)) {
                    list($code, $value) = $expect;
                    $expect = NULL;
                    goto repeat;
                }
            }
            return false;
        }
        return true;
    }

    public function reset(): self {
        $this->tokens = [];
        return $this;
    }

    /**
     * Can be a code, content, an array with [code, content] or a callback
     * @param $token
     */
    public function add($token): self {
        if(is_array($token) || is_numeric($token) || is_callable($token) || is_string($token))
            $this->tokens[] = $token;
        return $this;
    }

    public function append(TokenSet $otherSet): self {
        $this->tokens = array_merge($this->tokens, $otherSet->tokens);
        return $this;
    }

    public function addFrom(...$arguments): self {
        foreach($arguments as $args)
            $this->add($args);
        return $this;
    }
}