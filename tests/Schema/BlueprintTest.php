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

    protected function setUp()
    {
        $this->blueprint = new Blueprint('some_table');
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

    public function testUniqueReturnsCorrectFluent()
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
}
