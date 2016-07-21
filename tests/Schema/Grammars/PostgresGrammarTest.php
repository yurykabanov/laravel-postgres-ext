<?php

namespace YuryKabanov\Database\Schema\Grammars;

use PHPUnit\Framework\TestCase;

use Illuminate\Support\Fluent;

use YuryKabanov\Database\Schema\Blueprint;

class PostgresGrammarTest extends TestCase
{
    /**
     * @var PostgresGrammar
     */
    private $grammar;

    /**
     * @var Blueprint
     */
    private $blueprint;

    protected function setUp()
    {
        $this->grammar = new PostgresGrammar();
        $this->blueprint = $this->createMock(Blueprint::class);
        $this->blueprint->method('getTable')->willReturn('some_table');
    }

    public function testCompileIndexDefault()
    {
        $compiled = $this->compileIndexUniqueUsing('index');

        $expected = 'create index '
            . '"some_table_some_column_index" on "some_table" '
            . 'using btree ("some_column")';

        $this->assertEquals($expected, $compiled);
    }

    public function testCompileIndexConcurrently()
    {
        $compiled = $this->compileIndexUniqueUsing('index', 'btree', true);

        $expected = 'create index concurrently '
            . '"some_table_some_column_index" on "some_table" '
            . 'using btree ("some_column")';

        $this->assertEquals($expected, $compiled);
    }

    public function testCompileIndexGist()
    {
        $compiled = $this->compileIndexUniqueUsing('index', 'gist');

        $expected = 'create index '
            . '"some_table_some_column_index" on "some_table" '
            . 'using gist ("some_column")';

        $this->assertEquals($expected, $compiled);
    }

    public function testCompileIndexUnsupported() {
        $compiled = $this->compileIndexUniqueUsing('index', 'definitely_unsupported_index_method');

        $expected = 'create index '
            . '"some_table_some_column_index" on "some_table" '
            . 'using btree ("some_column")';

        $this->assertEquals($expected, $compiled);
    }

    public function testCompileUniqueIndexDefault()
    {
        $compiled = $this->compileIndexUniqueUsing('unique', 'btree', false, true);

        $expected = 'create unique index '
            . '"some_table_some_column_unique" on "some_table" '
            . 'using btree ("some_column")';

        $this->assertEquals($expected, $compiled);
    }

    /**
     * @param string $type
     * @param string $method
     * @param bool $concurrently
     * @param bool $unique
     * @return string
     */
    private function compileIndexUniqueUsing($type, $method = 'btree', $concurrently = false, $unique = false)
    {
        $fluent = new Fluent([
            'name' => $type,
            'index' => "some_table_some_column_$type",
            'columns' => ['some_column'],
            'method' => $method,
            'options' => [
                'unique' => $unique,
                'concurrently' => $concurrently
            ]
        ]);

        return $this->grammar->compileIndex($this->blueprint, $fluent);
    }

}
