<?php
/**
 * @author Todd Burry <todd@vanillaforums.com>
 * @copyright 2009-2015 Vanilla Forums Inc.
 * @license Proprietary
 */

namespace Nbbc\Tests;


use Nbbc\BBCode;

class UtilityMethodTest extends \PHPUnit_Framework_TestCase {
    /**
     * Provide data for {@link testIsValidTLDValid()} that should be valid.
     *
     * @return array Returns the test data.
     */
    public function provideTLDValidTests() {
        $result = [
            'localhost' => ['localhost'],
            '.com' => ['example.com'],
            'IP address' => ['127.0.0.1', true],
            '2 letter tld' => ['google.ca']
        ];
        return $result;
    }

    /**
     * Provide data for {@link testIsValidTLDInvalid()} that should be valid.
     *
     * @return array Returns the test data.
     */
    public function provideTLDInvalidTests() {
        $result = [
            '1 word' => ['local'],
            '.bar' => ['example.bar'],
            'IP address not allowed' => ['127.0.0.1'],
            'invalid IP address' => ['500.500.0.1', true],
            '2 character tld' => ['google.c0']
        ];
        return $result;
    }

    /**
     * Test {@link BBCode::isValidTLD()}.
     *
     * @param string $host The host to test.
     * @param bool $allowIPs Whether or not IP addresses count as valid IDs.
     * @dataProvider provideTLDValidTests
     */
    public function testIsValidTLDValid($host, $allowIPs = false) {
        $bbCode = new BBCode();

        $valid = $bbCode->isValidTLD($host, $allowIPs);
        $this->assertTrue($valid);
    }

    /**
     * Test {@link BBCode::isValidTLD()}.
     *
     * @param string $host The host to test.
     * @param bool $allowIPs Whether or not IP addresses count as valid IDs.
     * @dataProvider provideTLDInvalidTests
     */
    public function testIsValidTLDInvalid($host, $allowIPs = false) {
        $bbCode = new BBCode();

        $valid = $bbCode->isValidTLD($host, $allowIPs);
        $this->assertFalse($valid);
    }
}
