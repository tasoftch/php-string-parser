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

namespace TASoft\Parser\Tokenizer;


use TASoft\Parser\Token\TokenInterface;

abstract class AbstractTokenStackingTokenizer implements TokenizerInterface
{
    /** @var TokenizerInterface */
    private $tokenizer;

    /**
     * AbstractTokenPreparationTokenizer constructor.
     * @param TokenizerInterface $tokenizer
     */
    public function __construct(TokenizerInterface $tokenizer)
    {
        $this->tokenizer = $tokenizer;
    }

    /**
     * @inheritDoc
     */
    public function setScript(string $script)
    {
        $this->getTokenizer()->setScript($script);
    }

    /**
     * @inheritDoc
     */
    public function rewindTokenizer()
    {
        $this->getTokenizer()->rewindTokenizer();
    }

    /**
     * @inheritDoc
     */
    public function yieldToken(): \Generator
    {
        $stack = [];
        foreach($this->getTokenizer()->yieldToken() as $token) {
            if($this->shouldStackToken($token, $stack))
                continue;
            else
                yield $token;
        }
    }

    /**
     * @return TokenizerInterface
     */
    public function getTokenizer(): TokenizerInterface
    {
        return $this->tokenizer;
    }

    /**
     * This method decides if the incoming token should be stacked or forwarded.
     * If it returns true, the tokenizer continues yielding the next token, stacking the current token is up to this method!
     * If it returns false, the tokenizer forwards the token.
     *
     * @param TokenInterface $newToken
     * @param array $tokenStack
     * @return bool
     */
    abstract protected function shouldStackToken(TokenInterface &$nextToken, array &$tokenStack): bool;
}