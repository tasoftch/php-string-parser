<?php
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
        $codes = [0, 379, 328, 323, 100];

        foreach($php->yieldToken() as $token) {
            $code = next($codes);
            $this->assertEquals($code, $token->getCode());
        }
    }
}
