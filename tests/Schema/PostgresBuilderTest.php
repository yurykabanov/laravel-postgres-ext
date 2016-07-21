<?php

namespace YuryKabanov\Database\Schema;

use PHPUnit\Framework\TestCase;

use YuryKabanov\Database\PostgresConnection;
use YuryKabanov\Database\Schema\Grammars\PostgresGrammar;

class PostgresBuilderTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $connection;

    /**
     * @var PostgresBuilder
     */
    private $builder;

    protected function setUp()
    {
        $grammar = new PostgresGrammar();

        $this->connection = $this->createMock(PostgresConnection::class);
        $this->connection->method('getSchemaGrammar')->willReturn($grammar);

        $this->builder = new PostgresBuilder($this->connection);
    }

    public function testCreateView()
    {
        $this->connection->expects($this->once())
            ->method('statement')->with('create view "some_view" as select 1');

        $this->builder->createView('some_view', 'select 1');
    }

    public function testDropView()
    {
        $this->connection->expects($this->once())
            ->method('statement')->with('drop view "some_view"');

        $this->builder->dropView('some_view');
    }
}
