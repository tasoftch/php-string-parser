<?php

namespace TASoft\Parser\Exception;

use TASoft\Parser\AbstractParser;
use Throwable;

class ParserException extends \RuntimeException
{
    /** @var AbstractParser */
    private $parser;

    public function __construct(string $message = "", int $code = 0, Throwable $previous = NULL, ...$args)
    {
        parent::__construct(vsprintf($message, $args), $code, $previous);
    }

    /**
     * @return AbstractParser
     */
    public function getParser(): AbstractParser
    {
        return $this->parser;
    }

    /**
     * @param AbstractParser $parser
     */
    public function setParser(AbstractParser $parser): void
    {
        $this->parser = $parser;
    }
}