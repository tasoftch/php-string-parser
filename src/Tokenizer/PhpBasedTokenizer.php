<?php

namespace TASoft\Parser\Tokenizer;

/**
 * Class PhpBasedTokenizer uses the build in token_get_all function to create tokens
 * @package TASoft\Parser
 */
class PhpBasedTokenizer extends AbstractAtomicRawTokenizer
{
    /**
     * @inheritdoc
     */
    protected function getRawTokens(): array
    {
        return token_get_all( $this->getScript() );
    }
}