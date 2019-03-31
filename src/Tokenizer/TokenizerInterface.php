<?php

namespace TASoft\Parser\Tokenizer;


interface TokenizerInterface
{
    /**
     * The tokenizer should yield token by token and the parser will handle them.
     *
     */
    public function yieldToken(): \Generator;

    /**
     * Before the parser starts to iterate over tokens, it will rewind the tokenizer
     * @return void
     */
    public function rewindTokenizer();
}