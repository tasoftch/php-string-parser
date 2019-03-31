<?php

namespace TASoft\Parser;


use TASoft\Parser\Error\ErrorInterface;
use TASoft\Parser\Error\FatalError;
use TASoft\Parser\Exception\ParserAbortException;

abstract class AbstractParser
{
    private $errors = [];

    protected function addError(ErrorInterface $error) {
        $this->errors[] = $error;
        if($error instanceof FatalError) {
            $e = new ParserAbortException($error->getMessage(), $error->getCode());
            $e->setParser($this);
            throw $e;
        }
    }

    public function getErrors(): array {
        return $this->errors;
    }
}