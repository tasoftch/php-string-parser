<?php
/**
 * PhpTokenToObjectTransformerTest.php
 * php-parser
 *
 * Created on 31.03.19 17:53 by thomas
 */

namespace Transformer;

use TASoft\Parser\Token\RawToken;
use TASoft\Parser\Token\TokenInterface;
use TASoft\Parser\Tokenizer\Transformer\PhpTokenToObjectTransformer;
use PHPUnit\Framework\TestCase;

class PhpTokenToObjectTransformerTest extends TestCase
{

    public function testGetTransformedToken()
    {
        $transformer = new PhpTokenToObjectTransformer();

        $this->assertInstanceOf(TokenInterface::class, $transformer->getTransformedToken([T_VARIABLE, '$variable', 19]));
        $this->assertInstanceOf(TokenInterface::class, $token = $transformer->getTransformedToken("("));

        $this->assertEquals(RawToken::T_CONTROL, $token->getCode());
    }
}
