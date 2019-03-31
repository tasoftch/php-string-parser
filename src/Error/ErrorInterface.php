<?php

namespace TASoft\Parser\Error;


use TASoft\Parser\Token\TokenInterface;

interface ErrorInterface
{
    public function getMessage(): string;
    public function getCode(): int;

    public function getToken(): ?TokenInterface;
}