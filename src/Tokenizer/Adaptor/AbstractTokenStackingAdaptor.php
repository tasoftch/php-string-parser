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

namespace TASoft\Parser\Tokenizer\Adaptor;


use TASoft\Parser\Token\Token;
use TASoft\Parser\Token\TokenInterface;

abstract class AbstractTokenStackingAdaptor extends AbstractTokenizerAdaptor
{
    protected $tokenStack = [];
    private $flush = [];

    public function yieldToken(): \Generator
    {
        $transform = function($token) {
            $token = $this->getTransformer()->getTransformedToken($token);

            if($filters = $this->getFilters()) {
                foreach($filters as $filter) {
                    if(!$filter->shouldParseToken($token))
                        return NULL;
                }
            }
            return $token;
        };

        foreach($this->getTokenizer()->yieldToken() as $token) {
            if($this->flush) {
                foreach ($this->flush as $tk) {
                    if($t =  $transform($tk))
                        yield $t;
                }
                $this->flush = [];
            }

            $token = $this->adaptNextToken($token);
            if(!$token)
                continue;
            else {
                if($t =  $transform($token))
                    yield $t;
            }
        }
        if($this->flush) {
            foreach ($this->flush as $tk) {
                if($t =  $transform($tk))
                    yield $t;
            }
        }
        if($this->tokenStack) {
            foreach ($this->tokenStack as $tk) {
                if($t =  $transform($tk))
                    yield $t;
            }
        }
    }

    /**
     * Implementing this method, you are allowed to stack, join and flush tokens using the protected methods of this class.
     *
     * @param TokenInterface $token
     * @return TokenInterface|null
     */
    abstract public function adaptNextToken($token);

    /**
     * Adds a token to the stack
     *
     * @param $token
     */
    protected function pushToken($token) {
        $this->tokenStack[] = $token;
    }

    /**
     * Joins the passed token's content with the last token in stack.
     * If no token in stack, adds it.
     * strings, arrays and TokenInterfaces are joinable. Passing a code and/or a line will override the current ones
     *
     * @param $token
     * @param null $code
     * @param null $line
     */
    protected function joinToken($token, $code = NULL, $line = NULL) {
        if($last = $this->popToken()) {
            if($token instanceof TokenInterface)
                $content = $token->getContent();
            elseif (is_array($token))
                $content = $token[1];
            else
                $content = (string)$token;

            if($last instanceof Token) {
                (function() use ($content, $code, $line) {
                    $this->content .= $content;
                    if(NULL !== $code)
                        $this->code = $code;
                    if(NULL !== $line)
                        $this->line = $line;
                })->bindTo($last, Token::class)();
            } elseif (is_array($last)) {
                $last[0] = $code ?? $last[0];
                $last[1] .= $content;
                $last[2] = $line ?? $last[2];
            } else {
                $last .= $content;
            }

            $this->pushToken($last);
        } else {
            $this->pushToken($token);
        }
    }

    /**
     * Gets the last token from stack
     *
     * @return mixed
     */
    protected function getToken() {
        return end($this->tokenStack);
    }

    /**
     * Removes the last token from stack and returns it
     *
     * @return mixed
     */
    protected function popToken() {
        return array_pop($this->tokenStack);
    }

    /**
     * Removes the first token from stack and returns it
     *
     * @return mixed
     */
    protected function shiftToken() {
        return array_shift($this->tokenStack);
    }

    /**
     * Waits for the next iteration and sends the passed token before continuing adapting
     *
     * @param $token
     */
    protected function flushToken($token) {
        $this->flush[] = $token;
    }

    /**
     * Flushed the whole stack and then continues with adapting
     */
    protected function flushStack() {
        $this->flush = $this->tokenStack;
        $this->tokenStack = [];
    }
}