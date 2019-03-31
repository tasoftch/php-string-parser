<?php

namespace TASoft\Parser\Tokenizer\Transformer;


use TASoft\Parser\Token\TokenInterface;

interface TransformerInterface
{
    /**
     * Transforms anything to a usable token for the parser.
     *
     * @param $token
     * @return TokenInterface|null
     */
    public function getTransformedToken($token): ?TokenInterface;
}