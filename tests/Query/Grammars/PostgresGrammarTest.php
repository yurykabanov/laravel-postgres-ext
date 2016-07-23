<?php

namespace YuryKabanov\Database\Query\Grammars;

use PHPUnit\Framework\TestCase;
use YuryKabanov\Database\Query\Builder;
use YuryKabanov\Database\PostgresConnection;

class PostgresGrammarTest extends TestCase
{
    public function testCompileUpsert()
    {
        $grammar = new PostgresGrammar;

        $values = [
            'some' => 123,
            'uniq' => 'aaa',
            'created_at' => '2000-01-01 00:00:00',
            'updated_at' => '2000-01-01 00:00:00'
        ];

        $sql = 'insert into "some_table" ("some", "uniq", "created_at", "updated_at") '
            . 'values (?, ?, ?, ?) '
            . 'on conflict (uniq) do '
            . 'update set "some" = "excluded"."some", "updated_at" = "excluded"."updated_at"';

        $compiled = $grammar->compileUpsert($this->makeQueryMock(), $values, 'uniq');

        $this->assertEquals($sql, $compiled);
    }

    public function testWhereJsonbExistsOperator()
    {
        $builder = new Builder($this->makeConnectionMock(), new PostgresGrammar);
        $builder->where('some_field', '?', 'some_value');

        $compiled = $builder->toSql();

        $this->assertEquals('select * where jsonb_exists("some_field", ?)', $compiled);
    }

    public function testWhereJsonbExistsAnyOperator()
    {
        $builder = new Builder($this->makeConnectionMock(), new PostgresGrammar);
        $builder->where('some_field', '?|', 'some_value');

        $compiled = $builder->toSql();

        $this->assertEquals('select * where jsonb_exists_any("some_field", ?)', $compiled);
    }

    public function testWhereJsonbExistsAllOperator()
    {
        $builder = new Builder($this->makeConnectionMock(), new PostgresGrammar);
        $builder->where('some_field', '?&', 'some_value');

        $compiled = $builder->toSql();

        $this->assertEquals('select * where jsonb_exists_all("some_field", ?)', $compiled);
    }

    private function makeQueryMock()
    {
        $query = $this->getMockBuilder(Builder::class)
            ->setConstructorArgs([ $this->makeConnectionMock() ])
            ->getMock();
        $query->from = 'some_table';

        return $query;
    }

    private function makeConnectionMock()
    {
        return $this->createMock(PostgresConnection::class);
    }
}
