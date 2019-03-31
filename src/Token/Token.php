<?php
/**
 * Copyright (c) 2019 TASoft Applications, Th. Abplanalp <info@tasoft.ch>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

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