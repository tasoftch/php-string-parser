<?php

namespace TASoft\Parser\Error;


use TASoft\Parser\Token\TokenInterface;

class Notice implements ErrorInterface
{
    /** @var string */
    private $message;

    /** @var TokenInterface|null */
    private $token;

    /** @var int */
    private $code;

    /**
     * AbstractError constructor.
     * @param string $message
     * @param TokenInterface $token
     */
    public function __construct(string $message, int $code, TokenInterface $token = NULL)
    {
        $this->message = $message;
        $this->token = $token;
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return TokenInterface|null
     */
    public function getToken(): ?TokenInterface
    {
        return $this->token;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }
}