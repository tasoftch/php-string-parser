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

namespace TASoft\Parser;


use TASoft\Parser\Error\ErrorInterface;
use TASoft\Parser\Error\FatalError;
use TASoft\Parser\Exception\ParserAbortException;
use TASoft\Parser\Exception\ParserException;
use TASoft\Parser\Exception\UnexpectedTokenException;
use TASoft\Parser\Token\TokenInterface;
use TASoft\Parser\Tokenizer\PhpExpressionBasedTokenizer;
use TASoft\Parser\Tokenizer\TokenizerInterface;
use TASoft\Parser\Tokenizer\Transformer\PhpTokenToObjectTransformer;
use TASoft\Parser\TokenSet\TokenSet;
use TASoft\Parser\TokenSet\TokenSetInterface;

abstract class AbstractParser
{
    const MATCH_CASE_INSENSITIVE_CONTENT = 1<<0;

    /** @var TokenizerInterface */
    private $tokenizer;

    private $errors = [];
    private $_expects;

    /** @var TokenSetInterface|null */
    private $ignoredTokenSet;

    /**
     * @return null|TokenSetInterface
     */
    public function getIgnoredTokenSet(): ?TokenSetInterface
    {
        return $this->ignoredTokenSet;
    }

    /**
     * @param null|TokenSetInterface $ignoredTokenSet
     */
    public function setIgnoredTokenSet(?TokenSetInterface $ignoredTokenSet): void
    {
        $this->ignoredTokenSet = $ignoredTokenSet;
    }

    /**
     * AbstractParser constructor.
     * @param TokenizerInterface $tokenizer
     */
    public function __construct(TokenizerInterface $tokenizer = NULL)
    {
        $this->tokenizer = $tokenizer;
    }


    /**
     * @return TokenizerInterface
     */
    public function getTokenizer(): TokenizerInterface
    {
        if(!$this->tokenizer) {
            $this->tokenizer = new PhpExpressionBasedTokenizer();
            $this->tokenizer->setTransformer(new PhpTokenToObjectTransformer());
        }

        return $this->tokenizer;
    }

    /**
     * @param TokenizerInterface $tokenizer
     */
    public function setTokenizer(TokenizerInterface $tokenizer): void
    {
        $this->tokenizer = $tokenizer;
    }

    protected function addError(ErrorInterface $error) {
        $this->errors[] = $error;
        if($error instanceof FatalError) {
            $e = new ParserAbortException($error->getMessage(), $error->getCode());
            $e->setParser($this);
            throw $e;
        }
    }

    public function parseString(string $script, int $options = 0) {
        $tokenizer = $this->getTokenizer();
        $tokenizer->setScript($script);
        $tokenizer->rewindTokenizer();

        $this->parserDidStart();
        try {
            /** @var TokenInterface $token */
            foreach($tokenizer->yieldToken() as $token) {
                if($this->ignoreToken($token, $options))
                    continue;

                if($this->tokenMatchExpected($token, $options)) {
                    $this->_expects = NULL;

                    $this->parseToken($token, $options);
                } else {
                    $e = new UnexpectedTokenException("Unexpected token %s %s on line %d", 13, NULL, token_name($token->getCode()), $token->getContent(), $token->getLine());
                    $e->setToken($token);
                    throw $e;
                }
            }

            $fin = $this->parserWillFinish($this->errors);
            return $this->parserDidComplete($fin);
        } catch (ParserException $exception) {
            $exception->setParser($this);
            throw $exception;
        }
    }

    protected function ignoreToken(TokenInterface $token, int $options): bool
    {
        return ($ts = $this->getIgnoredTokenSet()) ? $ts->tokenIsMember($token) : false;
    }

    /**
     * Called before iterating over tokens
     */
    protected function parserDidStart() {
    }

    /**
     * Called after iterating over tokens
     *
     * @param array $errors
     * @return bool
     */
    protected function parserWillFinish(array $errors) {
        return count($errors) ? false : true;
    }

    /**
     * Called after return from parsing. The return value will be returned also.
     * @param bool $success
     * @return mixed
     */
    protected function parserDidComplete(bool $success) {
        return $success;
    }

    /**
     * Declare, what tokens are expected for next iteration.
     * You can pass integers as token codes, strings as token contents or arrays with an integer and a string for code AND content
     * You are also allowed to pass a callback!
     *
     * @param mixed ...$contents
     */
    protected function setNextExpected(...$contents) {
        ($this->_expects = ($this->_expects ?: new TokenSet()))->addFrom(...$contents);
    }

    /**
     * This method decides if the current token is valid (expected) or not.
     *
     * @param TokenInterface $token
     * @param int $options
     * @return bool
     */
    protected function tokenMatchExpected(TokenInterface $token, int $options): bool {
        if($expects = $this->_expects) {
            /** @var TokenSet $expects */
            return $expects->tokenIsMember($token);
        }
        return true;
    }

    /**
     * If the current token matches to the expectations, the parser will call this method do something with it.
     * In this method you should define the next expected stuff.
     *
     * @param TokenInterface $token
     * @param int $options
     * @return void
     * @see AbstractParser::setNextExpected()
     */
    abstract protected function parseToken(TokenInterface $token, int $options);
}