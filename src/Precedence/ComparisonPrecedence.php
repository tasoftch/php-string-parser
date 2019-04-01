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

namespace TASoft\Parser\Precedence;


class ComparisonPrecedence extends AbstractStaticPrecedence
{
    protected function load()
    {
        $this->precedences = [
            T_IS_EQUAL              => self::PREC_VERY_LOW,
            T_IS_IDENTICAL          => self::PREC_VERY_LOW,
            T_IS_NOT_EQUAL          => self::PREC_VERY_LOW,
            T_IS_NOT_IDENTICAL      => self::PREC_VERY_LOW,
            "<"                     => self::PREC_LOW,
            ">"                     => self::PREC_LOW,
            T_IS_GREATER_OR_EQUAL   => self::PREC_LOW,
            T_IS_SMALLER_OR_EQUAL   => self::PREC_LOW,
        ];

        $this->associativities = [
            T_IS_EQUAL              => self::ASSOC_NONE,
            T_IS_GREATER_OR_EQUAL   => self::ASSOC_NONE,
            T_IS_IDENTICAL          => self::ASSOC_NONE,
            T_IS_NOT_EQUAL          => self::ASSOC_NONE,
            T_IS_NOT_IDENTICAL      => self::ASSOC_NONE,
            T_IS_SMALLER_OR_EQUAL   => self::ASSOC_NONE,
            "<"                     => self::ASSOC_NONE,
            ">"                     => self::ASSOC_NONE
        ];
    }
}