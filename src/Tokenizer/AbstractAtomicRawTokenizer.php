<?php

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