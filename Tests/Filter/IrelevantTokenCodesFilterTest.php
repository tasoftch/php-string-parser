<?php
/**
 * IrelevantTokenCodesFilterTest.php
 * php-parser
 *
 * Created on 31.03.19 17:28 by thomas
 */

namespace Filter;

use TASoft\Parser\Token\Token;
use TASoft\Parser\Tokenizer\Filter\IrelevantTokenCodesFilter;
use PHPUnit\Framework\TestCase;

class IrelevantTokenCodesFilterTest extends TestCase
{

    public function testShouldParseToken()
    {
        $filter = new IrelevantTokenCodesFilter();

        $this->assertFalse($filter->shouldParseToken(new Token(T_COMMENT, "", 1)));
        $this->assertFalse($filter->shouldParseToken(new Token(T_DOC_COMMENT, "", 1)));
        $this->assertFalse($filter->shouldParseToken(new Token(T_WHITESPACE, "", 1)));
        $this->assertFalse($filter->shouldParseToken(new Token(T_CLOSE_TAG, "", 1)));
        $this->assertTrue($filter->shouldParseToken(new Token(T_NUM_STRING, "", 1)));
        $this->assertTrue($filter->shouldParseToken(new Token(T_CONSTANT_ENCAPSED_STRING, "", 1)));
    }
}
