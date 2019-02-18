<?php

use cse\helpers\IP;
use PHPUnit\Framework\TestCase;

class TestIP extends TestCase
{
    /**
     * @param array $server
     * @param string $expected
     *
     * @dataProvider  providerGetRealIp
     *
     * @runInSeparateProcess
     */
    public function testGetRealIp(array $server, string $expected): void
    {
        $_SERVER = array_merge($_SERVER, $server);

        $this->assertEquals($expected, IP::getRealIP());
    }

    /**
     * @return array
     */
    public function providerGetRealIp(): array
    {
        return [
            [
                [
                    'HTTP_X_REAL_IP' => '',
                    'HTTP_CLIENT_IP' => '',
                    'HTTP_X_FORWARDED_FOR' => '10.10.10.160, 10.10.10.161, 10.10.10.162',
                ],
                '10.10.10.162',
            ],
        ];
    }

    /**
     * @param array $server
     * @param string $expected
     *
     * @dataProvider  providerRemoveSubnetMaskIPv6
     *
     * @runInSeparateProcess
     */
    public function testRemoveSubnetMaskIPv6(string $ip, string $expected): void
    {
        $this->assertEquals($expected, IP::removeSubnetMaskIPv6($ip));
    }

    /**
     * @return array
     */
    public function providerRemoveSubnetMaskIPv6(): array
    {
        return [
            [
                '2a0a:2b40::4:60',
                '2a0a:2b40::4:60'
            ],
            [
                '2a0a:2b40::4:60/124',
                '2a0a:2b40::4:60'
            ],
        ];
    }

    /**
     * @param string $ip
     * @param bool $expected
     *
     * @dataProvider providerIsIPv4
     */
    public function testIsIPv4(string $ip, bool $expected): void
    {
        $this->assertEquals($expected, IP::isIPv4($ip));
    }

    /**
     * @return array
     */
    public function providerIsIPv4(): array
    {
        return [
            [
                '127.0.0.1',
                true,
            ],
            [
                '255.255.255.255',
                true,
            ],
            [
                '256.256.256.256',
                false,
            ],
            [
                '2a0a:2b40::4:60',
                false,
            ],
        ];
    }

    /**
     * @param string $ip
     * @param bool $expected
     *
     * @dataProvider providerIsIPv6
     */
    public function testIsIPv6(string $ip, bool $expected): void
    {
        $this->assertEquals($expected, IP::isIPv6($ip));
    }

    /**
     * @return array
     */
    public function providerIsIPv6(): array
    {
        return [
            [
                '::',
                true,
            ],
            [
                '::1',
                true,
            ],
            [
                '::ffff:192.0.2.1',
                true,
            ],
            [
                '2a0a:2b40::4:60',
                true,
            ],
            [
                '0:0:0:0:0:0:0:1',
                true,
            ],
            [
                ':',
                false,
            ],
            [
                '255.255.255.255',
                false,
            ],
            [
                '256.256.256.256',
                false,
            ],
        ];
    }

    /**
     * @param string $ip
     * @param int|null $expected
     *
     * @dataProvider providerGetVersionIP
     */
    public function testGetVersionIP(string $ip, ?int $expected): void
    {
        $this->assertEquals($expected, IP::getVersionIP($ip));
    }

    /**
     * @return array
     */
    public function providerGetVersionIP(): array
    {
        return [
            [
                '::',
                6,
            ],
            [
                '::1',
                6,
            ],
            [
                '::ffff:192.0.2.1',
                6,
            ],
            [
                '2a0a:2b40::4:60',
                6,
            ],
            [
                '0:0:0:0:0:0:0:1',
                6,
            ],
            [
                ':',
                null,
            ],
            [
                '127.0.0.1',
                4,
            ],
            [
                '255.255.255.255',
                4,
            ],
            [
                '256.256.256.256',
                null,
            ],
        ];
    }

    /**
     * @param string $ip
     * @param bool $expected
     *
     * @dataProvider providerIsIP
     */
    public function testIsIP(string $ip, bool $expected): void
    {
        $this->assertEquals($expected, IP::isIP($ip));
    }

    /**
     * @return array
     */
    public function providerIsIP(): array
    {
        return [
            [
                '::',
                true,
            ],
            [
                '::1',
                true,
            ],
            [
                '::ffff:192.0.2.1',
                true,
            ],
            [
                '2a0a:2b40::4:60',
                true,
            ],
            [
                '0:0:0:0:0:0:0:1',
                true,
            ],
            [
                ':',
                false,
            ],
            [
                '127.0.0.1',
                true,
            ],
            [
                '255.255.255.255',
                true,
            ],
            [
                '256.256.256.256',
                false,
            ],
        ];
    }

    /**
     * @param string $ip
     * @param int|null $version
     * @param array $expected
     *
     * @dataProvider providerFilterIPs
     */
    public function testFilterIPs(string $ip, ?int $version, array $expected): void
    {
        $this->assertEquals($expected, IP::filterIPs($ip));
    }

    /**
     * @return array
     */
    public function providerFilterIPs(): array
    {
        return [
            [
                [
                    '127.0.0.1',
                    '2a0a:2b40::4:60',
                    '255.255.255.255',
                    '2a0a:2b40::4:6f',
                    '256.256.256.256'
                ],
                null,
                [4 => ['127.0.0.1', '255.255.255.255'], 6 => ['2a0a:2b40::4:60', '2a0a:2b40::4:6f']],
            ],
            [
                [
                    '127.0.0.1',
                    '2a0a:2b40::4:60',
                    '255.255.255.255',
                    '2a0a:2b40::4:6f',
                    '256.256.256.256'
                ],
                4,
                [4 => ['127.0.0.1', '255.255.255.255']],
            ],
            [
                [
                    '127.0.0.1',
                    '2a0a:2b40::4:60',
                    '255.255.255.255',
                    '2a0a:2b40::4:6f',
                    '256.256.256.256'
                ],
                6,
                [6 => ['2a0a:2b40::4:60', '2a0a:2b40::4:6f']],
            ],
        ];
    }

    /**
     * @param string $ip
     * @param int|null $version
     * @param array $expected
     *
     * @dataProvider providerGetFirstIPByVersion
     */
    public function testGetFirstIPByVersion(string $ip, int $version, ?string $expected): void
    {
        $this->assertEquals($expected, IP::getFirstIPByVersion($ip));
    }

    /**
     * @return array
     */
    public function providerGetFirstIPByVersion(): array
    {
        return [
            [
                [
                    '256.256.256.256',
                    '127.0.0.1',
                    '2a0a:2b40::4:60',
                    '255.255.255.255',
                    '2a0a:2b40::4:6f',
                    '256.256.256.256'
                ],
                4,
                '127.0.0.1',
            ],
            [
                [
                    '256.256.256.256',
                    '127.0.0.1',
                    '2a0a:2b40::4:60',
                    '255.255.255.255',
                    '2a0a:2b40::4:6f',
                    '256.256.256.256'
                ],
                6,
                '2a0a:2b40::4:60',
            ],
            [
                [
                    '256.256.256.256',
                    '2a0a:2b40::4:60',
                    '2a0a:2b40::4:6f',
                    '256.256.256.256'
                ],
                4,
                null,
            ],
        ];
    }
}