<?php

namespace YuryKabanov\Database\Connectors;

use PHPUnit\Framework\TestCase;

use Illuminate\Container\Container;
use Illuminate\Database\MysqlConnection;
use YuryKabanov\Database\PostgresConnection;

class ConnectionFactoryTest extends TestCase {
    public function setUp() {
        $container = new Container();
        $this->factory = new ConnectionFactory($container);
    }

    public function testMakePostgresConnection() {
        $connection = $this->factory->make($this->makeConfig('pgsql'));

        $this->assertInstanceOf(PostgresConnection::class, $connection);
    }

    public function testMakeMysqlConnection() {
        $connection = $this->factory->make($this->makeConfig('mysql'));

        $this->assertInstanceOf(MysqlConnection::class, $connection);
    }

    public function testMakeUnsupportedConnectionException() {
        $this->expectException(\InvalidArgumentException::class);

        $this->factory->make($this->makeConfig('definitely-not-supported-driver'));
    }

    private function makeConfig($driver) {
        return [
            'driver' => $driver,
            'host' => 'localhost',
            'port' => '1234',
            'database' => 'test',
            'username' => 'test',
            'password' => 'test',
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ];
    }
}
