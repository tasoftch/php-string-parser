<?php

namespace TASoft\Parser\Token;

/**
 * The parser uses token objects to parse and compile to anything you want.
 * @package TASoft\Parser
 */
interface TokenInterface
{
    /**
     * The token code.
     * @return int
     * @see builtin PHP constants T_*
     */
    public function getCode(): int;

    /**
     * The token contents
     * @return null|string
     */
    public function getContent(): ?string;

    /**
     * The line number of the token
     * @return int
     */
    public function getLine(): int;
}