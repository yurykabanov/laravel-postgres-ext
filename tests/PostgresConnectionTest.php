<?php

namespace YuryKabanov\Database;

use PHPUnit\Framework\TestCase;

class PostgresConnectionTest extends TestCase
{
    /**
     * @var PostgresConnection
     */
    private $connection;

    public function setUp()
    {
        $this->connection = new PostgresConnection(null);
        $this->connection->useDefaultSchemaGrammar();
    }

    public function testDefaultQueryGrammar()
    {
        $this->assertInstanceOf(
            Query\Grammars\PostgresGrammar::class,
            $this->connection->getQueryGrammar()
        );
    }

    public function testDefaultSchemaGrammar()
    {
        $this->assertInstanceOf(
            Schema\Grammars\PostgresGrammar::class,
            $this->connection->getSchemaGrammar()
        );
    }

    public function testSchemaBuilder()
    {
        $this->assertInstanceOf(
            Schema\PostgresBuilder::class,
            $this->connection->getSchemaBuilder()
        );
    }
}
