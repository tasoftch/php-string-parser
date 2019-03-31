<?php

namespace TASoft\Parser\Tokenizer\Filter;


class WhitespaceFilter implements FilterInterface
{
    public function shouldParseToken(int $code, ?string $content, int $line): bool
    {
        return $code == T_WHITESPACE ? false : true;
    }
}