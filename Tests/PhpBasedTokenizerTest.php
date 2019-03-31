<?php
/**
 * PhpBasedTokenizerTest.php
 * php-parser
 *
 * Created on 31.03.19 16:48 by thomas
 */

use TASoft\Parser\Tokenizer\PhpBasedTokenizer;
use PHPUnit\Framework\TestCase;

class PhpBasedTokenizerTest extends TestCase
{
    public function testTokenizer() {
        $php = new PhpBasedTokenizer();
        $script = "<?php echo 'Hello World!'; ?>";

        $php->setScript($script);

        $this->assertEquals($script, $php->getScript());

    }
}
