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

/**
 * Class AbstractAtomicRawTokenizer can be used if the tokenizing process will result all tokens.
 *
 * @package TASoft\Parser
 */
abstract class AbstractAtomicRawTokenizer extends AbstractRawTokenizer
{
    /** @var null|array  */
    private $rawTokens = NULL;
    private $pos;

    /**
     * @inheritdoc
     */
    protected function nextToken()
    {
        return $this->rawTokens[$this->pos++] ?? NULL;
    }

    /**
     * @inheritdoc
     */
    public function rewindTokenizer()
    {
        $this->rawTokens = $this->getRawTokens();
        $this->pos = 0;
    }

    /**
     * This method is called on rewind the tokenizer and should return all tokens to parse.
     *
     * @return array
     */
    abstract protected function getRawTokens(): array;
}