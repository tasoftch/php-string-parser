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

namespace TASoft\Parser\Token;

/**
 * This class can be used if you want to handle raw tokens for example from token_get_all().
 * @package TASoft\Parser
 */
abstract class RawToken
{
    const TOKEN_CODE = 0;
    const TOKEN_CONTENT = 1;
    const TOKEN_LINE = 2;

    const T_UNKNOWN = -1;
    const T_CONTROL = 100;

    public static function getTokenCode($token) {
        if(is_array($token))
            return $token[static::TOKEN_CODE] ?? static::T_UNKNOWN;
        return static::T_UNKNOWN;
    }

    public static function getTokenContent($token) {
        if(is_array($token))
            return $token[static::TOKEN_CONTENT] ?? NULL;
        return $token;
    }

    public static function getTokenLine($token) {
        if(is_array($token))
            return $token[static::TOKEN_LINE] ?? 0;
        return 0;
    }
}