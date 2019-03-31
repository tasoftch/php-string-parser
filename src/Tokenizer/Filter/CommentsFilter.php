<?php

namespace TASoft\Parser\Tokenizer\Filter;


use TASoft\Parser\Token\TokenInterface;

class CommentsFilter implements FilterInterface
{
    public function shouldParseToken(TokenInterface $token): bool
    {
        return !in_array($token->getCode(), [
            T_COMMENT,
            T_DOC_COMMENT
        ]);
    }
}