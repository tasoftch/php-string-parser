#php-parser#
php-parser provides an abstract mechanism to tokenize and parse any kind of scripts you want.  
There are some implemented parsers ready to use for expressions.

````php
$parser = new SimpleTokenParser();
print_r($parser->parseString("12 + 13 / 6"));
````
