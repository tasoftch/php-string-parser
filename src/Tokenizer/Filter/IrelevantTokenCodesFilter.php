<?php

namespace TASoft\Parser\Tokenizer\Filter;


use TASoft\Parser\Token\TokenInterface;

class IrelevantTokenCodesFilter implements FilterInterface
{
    public function shouldParseToken(TokenInterface $token): bool
    {
        return !in_array($token->getCode(), [
            T_COMMENT,
            T_DOC_COMMENT,
            T_WHITESPACE,
            defined('T_BAD_CHARACTER') ? T_BAD_CHARACTER : NULL,
            T_CLOSE_TAG
        ]);
    }

}