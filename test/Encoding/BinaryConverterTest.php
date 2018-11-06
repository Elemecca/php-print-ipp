<?php
/* Copyright 2018 Sam Hanes
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Elemecca\PrintIpp\Encoding;


use PHPUnit\Framework\TestCase;

final class BinaryConverterTest extends TestCase
{
    public function byteProvider()
    {
        return [
            ["\x7F",  127], // maximum
            ["\x01",    1], // smallest positive
            ["\x00",    0], // zero
            ["\xFF",   -1], // largest negative
            ["\x80", -128], // minimum
        ];
    }

    /** @dataProvider byteProvider */
    public function testByteRead($input, $expected)
    {
        $this->assertEquals($expected, BinaryConverter::readByte($input));
    }

    /** @dataProvider byteProvider */
    public function testByteWrite($expected, $input)
    {
        $this->assertEquals(
            bin2hex($expected),
            bin2hex(BinaryConverter::writeByte($input))
        );
    }


    public function byteLimitProvider()
    {
        return [
            [ 128], // one past positive signed limit
            [-129], // one past negative signed limit
        ];
    }

    /**
     * @dataProvider byteLimitProvider
     * @expectedException \DomainException
     */
    public function testByteWriteLimit($value)
    {
        BinaryConverter::writeByte($value);
    }



    public function shortProvider()
    {
        return [
            ["\x7F\xFF",  32767], // maximum
            ["\x7F\x00",  32512], // isolate byte 0
            ["\x00\xFF",    255], // isolate byte 1
            ["\x00\x01",      1], // smallest positive
            ["\x00\x00",      0], // zero
            ["\xFF\xFF",     -1], // largest negative
            ["\xFF\x00",   -256], // isolate byte 0
            ["\x80\xFF", -32513], // isolate byte 1
            ["\x80\x00", -32768], // minimum
        ];
    }

    /** @dataProvider shortProvider */
    public function testShortRead($input, $expected)
    {
        $this->assertEquals($expected, BinaryConverter::readShort($input));
    }

    /** @dataProvider shortProvider */
    public function testShortWrite($expected, $input)
    {
        $this->assertEquals(
            bin2hex($expected),
            bin2hex(BinaryConverter::writeShort($input))
        );
    }


    public function shortLimitProvider()
    {
        return [
            [ 32768], // one past positive signed limit
            [-32769], // one past negative signed limit
        ];
    }

    /**
     * @dataProvider shortLimitProvider
     * @expectedException \DomainException
     */
    public function testShortWriteLimit($value)
    {
        BinaryConverter::writeShort($value);
    }


    public function intProvider()
    {
        return [
            ["\x7F\xFF\xFF\xFF",  2147483647], // maximum
            ["\x7F\x00\x00\x00",  2130706432], // isolate byte 0
            ["\x00\xFF\x00\x00",    16711680], // isolate byte 1
            ["\x00\x00\xFF\x00",       65280], // isolate byte 2
            ["\x00\x00\x00\xFF",         255], // isolate byte 3
            ["\x00\x00\x00\x01",           1], // smallest positive
            ["\x00\x00\x00\x00",           0], // zero
            ["\xFF\xFF\xFF\xFF",          -1], // largest negative
            ["\xFF\x00\x00\x00",   -16777216], // isolate byte 0
            ["\x80\xFF\x00\x00", -2130771968], // isolate byte 1
            ["\x80\x00\xFF\x00", -2147418368], // isolate byte 2
            ["\x80\x00\x00\xFF", -2147483393], // isolate byte 3
            ["\x80\x00\x00\x00", -2147483648], // minimum
        ];
    }

    /** @dataProvider intProvider */
    public function testIntRead($input, $expected)
    {
        $this->assertEquals($expected, BinaryConverter::readInt($input));
    }

    /** @dataProvider intProvider */
    public function testIntWrite($expected, $input)
    {
        $this->assertEquals(
            bin2hex($expected),
            bin2hex(BinaryConverter::writeInt($input))
        );
    }


    public function intLimitProvider()
    {
        return [
            [ 2147483648], // one past positive signed limit
            [-2147483649], // one past negative signed limit
        ];
    }

    /**
     * @dataProvider intLimitProvider
     * @expectedException \DomainException
     */
    public function testIntWriteLimit($value)
    {
        BinaryConverter::writeInt($value);
    }
}
