<?php

namespace TASoft\Parser\Tokenizer\Filter;


interface FilterInterface
{
    public function shouldParseToken(int $code, ?string $content, int $line): bool;
}