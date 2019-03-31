<?php

namespace TASoft\Parser\Tokenizer\Transformer;


use TASoft\Parser\Token\Token;
use TASoft\Parser\Token\TokenInterface;

/**
 * Tries to transform anything to a token object.
 * @package TASoft\Parser
 */
class TokenObjectTransformer implements TransformerInterface
{
    public function getTransformedToken($token): ?TokenInterface
    {
        if($token instanceof TokenInterface)
            return $token;

        return $token ? Token::create($token) : NULL;
    }
}