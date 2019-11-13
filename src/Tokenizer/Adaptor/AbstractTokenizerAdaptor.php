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


use TASoft\Parser\Tokenizer\Filter\FilterInterface;
use TASoft\Parser\Tokenizer\TokenizerInterface;
use TASoft\Parser\Tokenizer\Transformer\TokenObjectTransformer;
use TASoft\Parser\Tokenizer\Transformer\TransformerInterface;

abstract class AbstractTokenizerAdaptor implements AdaptorInterface
{
    /** @var TokenizerInterface */
    private $tokenizer;

    /** @var TransformerInterface|null */
    private $transformer;

    /** @var FilterInterface[] */
    private $filters = [];

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
        foreach($this->getTokenizer()->yieldToken() as $token) {
            $token = $this->adaptNextToken($token);
            if(!$token)
                continue;
            else {
                $token = $this->getTransformer()->getTransformedToken($token);

                if($filters = $this->getFilters()) {
                    foreach($filters as $filter) {
                        if(!$filter->shouldParseToken($token))
                            continue 2;
                    }
                }
                yield $token;
            }
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
     * @return TransformerInterface
     */
    public function getTransformer(): TransformerInterface
    {
        if($this->transformer === NULL)
            $this->transformer = new TokenObjectTransformer();
        return $this->transformer;
    }

    /**
     * @param null|TransformerInterface $transformer
     */
    public function setTransformer(?TransformerInterface $transformer): void
    {
        $this->transformer = $transformer;
    }

    /**
     * @return FilterInterface[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param FilterInterface[] $filters
     */
    public function setFilters(array $filters): void
    {
        $this->filters = $filters;
    }
}