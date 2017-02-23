<?php
/**
 * MailWatch2
 * Copyright (C) 2015 Manuel Dalla Lana <endelwar@aregar.it>
 *
 * This program is free software: you can redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your
 * option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program.
 * If not, see <http://www.gnu.org/licenses/>.
 */

namespace MailWatch\Test;

use MailWatch\Validate\Network;

class NetworkTest extends \PHPUnit_Framework_TestCase
{
    /** @var Network */
    private $networkValidator;

    protected function setUp()
    {
        $this->networkValidator = new Network();
    }

    /**
     * @param string $ip IP Address
     * @param bool $expectedResult Result
     *
     * @dataProvider ipProvider
     */
    public function testIsIp($ip, $expectedResult)
    {
        $this->assertSame($expectedResult, $this->networkValidator->isIp($ip));
    }

    /**
     * @param string $hostname
     * @param bool $expectedResult
     *
     * @dataProvider hostnameProvider
     */
    public function testIsHostname($hostname, $expectedResult)
    {
        $this->assertSame($expectedResult, $this->networkValidator->isHostname($hostname));
    }

    /**
     * @param string $hostname
     * @param bool $expectedResult
     *
     * @dataProvider ipOrHostnameProvider
     */
    public function testIsIpOrHostname($hostname, $expectedResult)
    {
        $this->assertSame($expectedResult, $this->networkValidator->isIpOrHostname($hostname));
    }

    /**
     * @param int $port
     * @param bool $expectedResult
     *
     * @dataProvider portProvider
     */
    public function testIsValidPort($port, $expectedResult)
    {
        $this->assertSame($expectedResult, $this->networkValidator->isValidPort($port));
    }

    public function ipProvider()
    {
        return array(
            array('127.0.0.1', true),
            array('192.168.1.1', true),
            array('257.896.567.1', false),
            array('192.168.1.1.123', false),
            array('::1', true),
            array('21DA:D3:0:2F3B:2AA:FF:FE28:9C5A', true),
            array('1200::AB00:1234::2552:7777:1313', false), // double use of ::
            array('2001:0:234:W1XY:0:A0:AABC:3F', false), // not hexadecimal values
        );
    }

    public function hostnameProvider()
    {
        return array(
            array('localhost', true),
            array('server1', true),
            array('1-server', true),
            array('mx.mylan.local', true),
            array('mailwatch.example.org', true),
            array('mai_lwatch.exa^mple.org', false), // unallowed chars
        );
    }

    public function ipOrHostnameProvider()
    {
        return array(
            array('127.0.0.1', true),
            array('257.896.567.1', true), // this is a valid hostname per RFC 1123
            array('21DA:D3:0:2F3B:2AA:FF:FE28:9C5A', true),
            array('1200::AB00:1234::2552:7777:1313', false), // double use of ::
            array('localhost', true),
            array('mai_lwatch.exa^mple.org', false), // unallowed chars
        );
    }

    public function portProvider()
    {
        return array(
            array(1, true),
            array(3306, true),
            array(65535, true),
            array(0, false), // unusable port
            array(65536, false), // unusable port
            array('ImAString', false), // not a number
        );
    }
}
