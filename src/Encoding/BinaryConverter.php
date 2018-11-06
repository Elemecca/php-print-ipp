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

/** Binary encoding/decoding helpers.
 * Unfortunately the built-in {@code pack} function doesn't support signed
 * big-endian integers, so we have to implement our own conversion.
 */
final class BinaryConverter
{
    const BYTE_MIN = -128;
    const BYTE_MAX = 127;
    const BYTE_COMP = 256;

    const SHORT_MIN = -32768;
    const SHORT_MAX = 32767;
    const SHORT_COMP = 65536;

    const INT_MIN = -2147483648;
    const INT_MAX = 2147483647;
    const INT_COMP = 4294967296;

    /** Unpacks a 1-byte signed big-endian integer (SIGNED-BYTE).
     *
     * @param string $bin a binary string of length (at least) 1
     * @return int an integer between -128 and 127
     */
    public static function readByte(string $bin): int
    {
        $value = ord($bin[0]);
        if ($value > self::BYTE_MAX) {
            $value -= self::BYTE_COMP;
        }
        return $value;
    }

    /** Unpacks a 2-byte signed big-endian integer (SIGNED-SHORT).
     *
     * @param string $bin a binary string of length (at least) 2
     * @return int an integer between -32,768 and 32,767
     */
    public static function readShort(string $bin): int
    {
        $value = (ord($bin[0]) << 8) | ord($bin[1]);
        if ($value > self::SHORT_MAX) {
            $value -= self::SHORT_COMP;
        }
        return $value;
    }

    /** Unpacks a 4-byte signed big-endian integer (SIGNED-INTEGER).
     *
     * @param string $bin a binary string of length (at least) 4
     * @return int an integer between -2,147,483,648 and 2,147,483,647
     */
    public static function readInt(string $bin): int
    {
        $value = (
            (ord($bin[0]) << 24)
            | (ord($bin[1]) << 16)
            | (ord($bin[2]) << 8)
            | ord($bin[3])
        );
        if ($value > self::INT_MAX) {
            $value -= self::INT_COMP;
        }
        return $value;
    }

    /** Packs a 1-byte signed big-endian integer (SIGNED-BYTE).
     *
     * @param int $value an integer between -128 and 127
     * @return string a binary string of length 1
     * @throws \DomainException if the given value is out of range
     */
    public static function writeByte(int $value): string
    {
        if ($value < self::BYTE_MIN || $value > self::BYTE_MAX) {
            throw new \DomainException(sprintf(
                "byte values must be between %d and %d, got %d",
                self::BYTE_MIN, self::BYTE_MAX, $value
            ));
        }

        if ($value < 0) {
            $value += self::BYTE_COMP;
        }

        return chr($value);
    }

    /** Packs a 2-byte signed big-endian integer (SIGNED-SHORT).
     *
     * @param int $value an integer between -32,768 and 32,767
     * @return string a binary string of length 2
     * @throws \DomainException if the given value is out of range
     */
    public static function writeShort(int $value): string
    {
        if ($value < self::SHORT_MIN || $value > self::SHORT_MAX) {
            throw new \DomainException(sprintf(
                "short values must be between %d and %d, got %d",
                self::SHORT_MIN, self::SHORT_MAX, $value
            ));
        }

        if ($value < 0) {
            $value += self::SHORT_COMP;
        }

        return chr(($value >> 8) & 0xFF) . chr($value & 0xFF);
    }

    /** Packs a 4-byte signed big-endian integer (SIGNED-INTEGER).
     *
     * @param int $value an integer between -2,147,483,648 and 2,147,483,647
     * @return string a binary string of length 4
     * @throws \DomainException if the given value is out of range
     */
    public static function writeInt(int $value): string
    {
        if ($value < self::INT_MIN || $value > self::INT_MAX) {
            throw new \DomainException(sprintf(
                "int values must be between %d and %d, got %d",
                self::INT_MIN, self::INT_MAX, $value
            ));
        }

        if ($value < 0) {
            $value += self::INT_COMP;
        }

        return (
            chr(($value >> 24) & 0xFF)
            . chr(($value >> 16) & 0xFF)
            . chr(($value >> 8) & 0xFF)
            . chr($value & 0xFF)
        );
    }

    private function __construct()
    {
    }
}
