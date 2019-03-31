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


use TASoft\Parser\Tokenizer\Filter\FilterInterface;
use TASoft\Parser\Tokenizer\Transformer\TokenObjectTransformer;
use TASoft\Parser\Tokenizer\Transformer\TransformerInterface;

abstract class AbstractRawTokenizer implements TokenizerInterface
{
    /** @var string */
    private $script;

    /** @var TransformerInterface|null */
    private $transformer;

    /** @var FilterInterface[] */
    private $filters = [];

    /**
     * AbstractTokenizer constructor.
     * @param string $script
     */
    public function __construct(string $script = NULL)
    {
        $this->script = $script;
    }

    /**
     * @return string
     */
    public function getScript(): string
    {
        return $this->script;
    }

    /**
     * @param string $script
     */
    public function setScript(string $script): void
    {
        $this->script = $script;
    }

    public function yieldToken(): \Generator
    {
        while($token = $this->nextToken()) {
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

    /**
     * Adds a filter to the list
     *
     * @param FilterInterface $filter
     */
    public function addFilter(FilterInterface $filter) {
        if(!in_array($filter, $this->filters))
            $this->filters[] = $filter;
    }

    /**
     * Removes a filter from list
     *
     * @param FilterInterface $filter
     */
    public function removeFilter(FilterInterface $filter) {
        if(($idx = array_search($filter, $this->filters)) !== false)
            unset($this->filters[$idx]);
    }

    /**
     * This method should provide the next token.
     * Returning NULL will mark end of script and terminates the parsing process.
     *
     * @return mixed|null
     */
    abstract protected function nextToken();
}