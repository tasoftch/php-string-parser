<?php

namespace TASoft\Parser\Tokenizer\Filter;


abstract class AbstractAcceptedTokenCodesFilter implements FilterInterface
{
    public function shouldParseToken(int $code, ?string $content, int $line): bool
    {
        return in_array($code, $this->getAcceptedTokenCodes());
    }

    abstract protected function getAcceptedTokenCodes(): array;
}