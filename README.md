# PHP String Parser
My php string parser library provides an abstract mechanism to tokenize, parse and until compile any kind of scripts you want.  
There are some implemented parsers ready to use for expressions.

````php
$parser = new SimpleTokenParser();
print_r($parser->parseString("12 + 13 / 6"));
````
This list all parsed tokens.  
Token parsing is done by php token_get_all function, but you can easy subclass a tokenizer to customize string tokenizing.

## Install
PHP String Parser is a composer package. So you can simply install it by
````bin
$ composer require tasoft/php-string-parser
````
Now the library is available under namespace \TASoft\Parser\...

### Examples
The package tasoft/predicate uses this parser to create predicates from string.