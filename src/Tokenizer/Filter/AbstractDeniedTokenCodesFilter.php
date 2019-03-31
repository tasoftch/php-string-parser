<?php

namespace TASoft\Parser\Tokenizer\Filter;


abstract class AbstractDeniedTokenCodesFilter implements FilterInterface
{
    public function shouldParseToken(int $code, ?string $content, int $line): bool
    {
        return !in_array($code, $this->getDeniedTokenCodes());
    }

    abstract protected function getDeniedTokenCodes(): array;
}