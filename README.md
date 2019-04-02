# PHP String Parser
My php string parser library provides an abstract mechanism to tokenize, parse and until compile any kind of scripts you want.  
There are some implemented parsers ready to use for expressions.

````php
$parser = new SimpleTokenParser();
print_r($parser->parseString("12 + 13 / 6"));
````
This list all parsed tokens.  
Token parsing is done by php token_get_all function, but you can easy subclass a tokenizer to customize string tokenizing.