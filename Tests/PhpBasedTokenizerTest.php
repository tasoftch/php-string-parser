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
 * PhpBasedTokenizerTest.php
 * php-parser
 *
 * Created on 31.03.19 16:48 by thomas
 */

use TASoft\Parser\Tokenizer\Filter\IrelevantTokenCodesFilter;
use TASoft\Parser\Tokenizer\PhpBasedTokenizer;
use PHPUnit\Framework\TestCase;
use TASoft\Parser\Tokenizer\Transformer\PhpTokenToObjectTransformer;

class PhpBasedTokenizerTest extends TestCase
{
    public function testTokenizer() {
        $php = new PhpBasedTokenizer();
        $script = "<?php echo 'Hello World!'; ?>";

        $php->setScript($script);

        $this->assertEquals($script, $php->getScript());

        $php->setTransformer(new PhpTokenToObjectTransformer());
        $php->addFilter(new IrelevantTokenCodesFilter());

        $php->rewindTokenizer();
        $codes = [T_ECHO, T_CONSTANT_ENCAPSED_STRING, 100];
		$cache = [];
        foreach($php->yieldToken() as $token) {
            $cache[] = $token->getCode();
        }
		$this->assertEquals($codes, $cache);
    }
}
