<?php

namespace TASoft\Parser\Tokenizer\Filter;


use TASoft\Parser\Token\TokenInterface;

abstract class AbstractDeniedTokenCodesFilter implements FilterInterface
{
    public function shouldParseToken(TokenInterface $token): bool
    {
        return !in_array($token->getCode(), $this->getDeniedTokenCodes());
    }

    abstract protected function getDeniedTokenCodes(): array;
}