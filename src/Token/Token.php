<?php

namespace TASoft\Parser\Token;


class Token implements TokenInterface
{
    /** @var int */
    private $code;
    /** @var string|null */
    private $content;
    /** @var int */
    private $line;

    /**
     * Token constructor.
     * @param int $code
     * @param null|string $content
     * @param int $line
     */
    public function __construct(int $code, ?string $content, int $line)
    {
        $this->code = $code;
        $this->content = $content;
        $this->line = $line;
    }

    /**
     * Creates a token
     *
     * @param $token
     * @return TokenInterface
     */
    public static function create($token): TokenInterface {
        return new static(
            RawToken::getTokenCode($token),
            RawToken::getTokenContent($token),
            RawToken::getTokenLine($token)
        );
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return null|string
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @return int
     */
    public function getLine(): int
    {
        return $this->line;
    }

    public function __toString()
    {
        switch($this->getCode()) {
            case RawToken::T_CONTROL: $name = "CONTROL"; break;
            case RawToken::T_UNKNOWN: $name = "UNKNOWN"; break;
            default:
                $name = token_name( $this->getCode() ); break;
        }

        $content = $this->getContent();

        return sprintf("%000d: %-50s %s", $this->getLine(), "$name(".$this->getCode().")", $content);
    }

    public function __debugInfo()
    {
        return [(string) $this];
    }
}