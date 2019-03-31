<?php

namespace TASoft\Parser\Tokenizer\Transformer;


use TASoft\Parser\Token\RawToken;
use TASoft\Parser\Token\Token;
use TASoft\Parser\Token\TokenInterface;

class PhpTokenToObjectTransformer implements TransformerInterface
{
    public function getTransformedToken($token): ?TokenInterface
    {
        if($token instanceof TokenInterface)
            return $token;

        static $line = 0;


        if(is_string($token))
            $token = [RawToken::T_CONTROL, $token, $line];
        else
            $line = RawToken::getTokenLine($token);

        return $token ? Token::create($token) : NULL;
    }
}