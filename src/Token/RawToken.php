<?php

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

    public static function getTokenCode($token) {
        if(is_array($token))
            return $token[static::TOKEN_CODE] ?? static::T_UNKNOWN;
        return static::T_UNKNOWN;
    }

    public static function getTokenContent($token) {
        if(is_array($token))
            return $token[static::TOKEN_CONTENT] ?? NULL;
        return NULL;
    }

    public static function getTokenLine($token) {
        if(is_array($token))
            return $token[static::TOKEN_LINE] ?? 0;
        return 0;
    }
}