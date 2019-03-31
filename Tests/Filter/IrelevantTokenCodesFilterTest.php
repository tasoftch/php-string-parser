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
