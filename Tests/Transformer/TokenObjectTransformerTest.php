<?php
/**
 * TokenObjectTransformerTest.php
 * php-parser
 *
 * Created on 31.03.19 17:30 by thomas
 */

namespace Transformer;

use TASoft\Parser\Token\TokenInterface;
use TASoft\Parser\Tokenizer\Transformer\TokenObjectTransformer;
use PHPUnit\Framework\TestCase;

class TokenObjectTransformerTest extends TestCase
{

    public function testGetTransformedToken()
    {
        $transformer = new TokenObjectTransformer();

        $this->assertInstanceOf(TokenInterface::class, $transformer->getTransformedToken([T_STRING, "Test", 23]));
    }
}
