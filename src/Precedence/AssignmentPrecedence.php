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


class AssignmentPrecedence extends AbstractStaticPrecedence
{
    protected function load()
    {
        $this->precedences = [
            "="                         => self::PREC_LOWEST + 10,
            T_PLUS_EQUAL                => self::PREC_LOWEST + 10,
            T_MINUS_EQUAL               => self::PREC_LOWEST + 10,
            T_MUL_EQUAL                 => self::PREC_LOWEST + 10,
            T_POW_EQUAL                 => self::PREC_LOWEST + 10,
            T_DIV_EQUAL                 => self::PREC_LOWEST + 10,
            T_CONCAT_EQUAL              => self::PREC_LOWEST + 10,
            T_MOD_EQUAL                 => self::PREC_LOWEST + 10,
            T_AND_EQUAL                 => self::PREC_LOWEST + 10,
            T_OR_EQUAL                  => self::PREC_LOWEST + 10,
            T_XOR_EQUAL                 => self::PREC_LOWEST + 10,
            T_SR_EQUAL                  => self::PREC_LOWEST + 10,
            T_SL_EQUAL                  => self::PREC_LOWEST + 10
        ];

        $this->associativities = [
            "="                         => self::ASSOC_RIGHT,
            T_PLUS_EQUAL                => self::ASSOC_RIGHT,
            T_MINUS_EQUAL               => self::ASSOC_RIGHT,
            T_MUL_EQUAL                 => self::ASSOC_RIGHT,
            T_POW_EQUAL                 => self::ASSOC_RIGHT,
            T_DIV_EQUAL                 => self::ASSOC_RIGHT,
            T_CONCAT_EQUAL              => self::ASSOC_RIGHT,
            T_MOD_EQUAL                 => self::ASSOC_RIGHT,
            T_AND_EQUAL                 => self::ASSOC_RIGHT,
            T_OR_EQUAL                  => self::ASSOC_RIGHT,
            T_XOR_EQUAL                 => self::ASSOC_RIGHT,
            T_SR_EQUAL                  => self::ASSOC_RIGHT,
            T_SL_EQUAL                  => self::ASSOC_RIGHT
        ];
    }
}