<?php

namespace TASoft\Parser\Tokenizer\Filter;


use TASoft\Parser\Token\TokenInterface;

interface FilterInterface
{
    public function shouldParseToken(TokenInterface $token): bool;
}