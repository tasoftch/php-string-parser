<?php

namespace TASoft\Parser\Tokenizer\Filter;


class IrelevantTokenCodesFilter implements FilterInterface
{
    public function shouldParseToken(int $code, ?string $content, int $line): bool
    {
        return !in_array($code, [
            T_COMMENT,
            T_DOC_COMMENT,
            T_WHITESPACE,
            defined('T_BAD_CHARACTER') ? T_BAD_CHARACTER : NULL,
            T_CLOSE_TAG
        ]);
    }

}