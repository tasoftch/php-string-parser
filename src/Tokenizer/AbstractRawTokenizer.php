<?php

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