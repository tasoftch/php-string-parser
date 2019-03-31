<?php

namespace TASoft\Parser\Tokenizer\Filter;


use TASoft\Parser\Token\TokenInterface;

abstract class AbstractAcceptedTokenCodesFilter implements FilterInterface
{
    public function shouldParseToken(TokenInterface $token): bool
    {
        return in_array($token->getCode(), $this->getAcceptedTokenCodes());
    }

    abstract protected function getAcceptedTokenCodes(): array;
}