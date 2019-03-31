<?php

namespace TASoft\Parser\Tokenizer\Filter;


class CommentsFilter implements FilterInterface
{
    public function shouldParseToken(int $code, ?string $content, int $line): bool
    {
        return !in_array($code, [
            T_COMMENT,
            T_DOC_COMMENT
        ]);
    }
}