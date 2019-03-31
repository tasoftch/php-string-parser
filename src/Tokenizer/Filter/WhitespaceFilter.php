<?php

namespace TASoft\Parser\Tokenizer\Filter;


use TASoft\Parser\Token\TokenInterface;

class WhitespaceFilter implements FilterInterface
{
    public function shouldParseToken(TokenInterface $token): bool
    {
        return $token->getCode() == T_WHITESPACE ? false : true;
    }
}