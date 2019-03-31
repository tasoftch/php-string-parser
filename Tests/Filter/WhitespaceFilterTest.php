<?php
/**
 * WhitespaceFilterTest.php
 * php-parser
 *
 * Created on 31.03.19 17:28 by thomas
 */

namespace Filter;

use TASoft\Parser\Tokenizer\Filter\WhitespaceFilter;
use TASoft\Parser\Token\Token;
use PHPUnit\Framework\TestCase;

class WhitespaceFilterTest extends TestCase
{
    public function testFilter() {
        $filter = new WhitespaceFilter();

        $this->assertFalse($filter->shouldParseToken(new Token(T_WHITESPACE, "", 1)));
        $this->assertTrue($filter->shouldParseToken(new Token(T_DOC_COMMENT, "", 1)));
        $this->assertTrue($filter->shouldParseToken(new Token(T_NUM_STRING, "", 1)));
        $this->assertTrue($filter->shouldParseToken(new Token(T_CONSTANT_ENCAPSED_STRING, "", 1)));
    }
}
