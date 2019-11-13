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

namespace TASoft\Parser\Tokenizer\Adaptor;


use TASoft\Parser\Token\TokenInterface;
use TASoft\Parser\Tokenizer\TokenizerInterface;

/**
 * The extended names adaptor can be used to simply combine T_STRING tokens with single characters.
 *
 *
 * @package TASoft\Parser\Tokenizer\Adaptor
 */
class ExtendedNamesAdaptor extends AbstractTokenStackingAdaptor
{
    /** @var string */
    private $nameCharacters;

    public function __construct(TokenizerInterface $tokenizer, string $nameCharacters = '-')
    {
        parent::__construct($tokenizer);
        $this->nameCharacters = $nameCharacters;
    }


    public function adaptNextToken($token)
    {
        if(is_array($token)) {
            if($token[0] == T_STRING )
                return $this->joinToken($token);
        }
        elseif($token instanceof TokenInterface) {
            if($token->getCode() == T_STRING)
                return $this->joinToken($token);
            elseif ($token->getCode() == -1 && strpos($this->getNameCharacters(), $token->getContent()) !== false)
                return $this->joinToken($token);
        }
        elseif(strpos($this->getNameCharacters(), $token) !== false)
            return $this->joinToken($token);

        $this->pushToken($token);
        $this->flushStack();
        return NULL;
    }

    /**
     * @return string
     */
    public function getNameCharacters(): string
    {
        return $this->nameCharacters;
    }
}