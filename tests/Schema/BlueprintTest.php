<?php

namespace YuryKabanov\Database\Schema;

use PHPUnit\Framework\TestCase;
use YuryKabanov\Database\PostgresConnection;
use YuryKabanov\Database\Schema\Grammars\PostgresGrammar;

class BlueprintTest extends TestCase
{
    /**
     * @var Blueprint
     */
    private $blueprint;

    /**
     * @var PostgresConnection
     */
    private $connection;

    protected function setUp()
    {
        $this->blueprint = new Blueprint('some_table');
        $this->connection = $this->createMock(PostgresConnection::class);
    }

    public function testIndexReturnsCorrectFluent()
    {
        $fluent_array = $this->blueprint->index('some_column', null, 'gist', [ 'concurrently' => true ])->toArray();

        $fluent_expected = [
            'name' => 'index',
            'index' => 'some_table_some_column_index',
            'columns' => [ 'some_column' ],
            'method' => 'gist',
            'options' => [
                'concurrently' => true
            ]
        ];

        $this->assertEquals($fluent_expected, $fluent_array);
    }

    public function testIndexUniqueReturnsCorrectFluent()
    {
        $fluent_array = $this->blueprint->index('some_column', null, 'gist', [ 'concurrently' => true, 'unique' => true ])->toArray();

        $fluent_expected = [
            'name' => 'index',
            'index' => 'some_table_some_column_index',
            'columns' => [ 'some_column' ],
            'method' => 'gist',
            'options' => [
                'concurrently' => true,
                'unique' => true
            ]
        ];

        $this->assertEquals($fluent_expected, $fluent_array);
    }

    public function testCreateView()
    {
        $fluent_array = $this->blueprint->createView('select 1 as some_value')->toArray();

        $fluent_expected = [
            'name' => 'createView',
            'select' => 'select 1 as some_value',
            'materialize' => false
        ];

        $this->assertEquals($fluent_expected, $fluent_array);

        $grammar = new PostgresGrammar();
        $this->assertEquals([ 'create view "some_table" as select 1 as some_value' ], $this->blueprint->toSql($this->connection, $grammar));
    }

    public function testCreateMaterializedView()
    {
        $fluent_array = $this->blueprint->createView('select 1 as some_value', true)->toArray();

        $fluent_expected = [
            'name' => 'createView',
            'select' => 'select 1 as some_value',
            'materialize' => true
        ];

        $this->assertEquals($fluent_expected, $fluent_array);

        $grammar = new PostgresGrammar();
        $this->assertEquals([ 'create materialized view "some_table" as select 1 as some_value' ], $this->blueprint->toSql($this->connection, $grammar));
    }

    public function testDropView()
    {
        $fluent_array = $this->blueprint->dropView()->toArray();

        $fluent_expected = [
            'name' => 'dropView'
        ];

        $this->assertEquals($fluent_expected, $fluent_array);

        $grammar = new PostgresGrammar();
        $this->assertEquals([ 'drop view "some_table"' ], $this->blueprint->toSql($this->connection, $grammar));
    }
}
