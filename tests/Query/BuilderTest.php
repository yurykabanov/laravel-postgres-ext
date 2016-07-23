<?php

namespace YuryKabanov\Database\Query;

use PHPUnit\Framework\TestCase;
use YuryKabanov\Database\PostgresConnection;

class BuilderTest extends TestCase
{
    /**
     * @var PostgresConnection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $connection;

    protected function setUp()
    {
        $this->connection = $this->getMockBuilder(PostgresConnection::class)
            ->setConstructorArgs([ null ])
            ->setMethods([ 'insert' ])
            ->getMock();
    }

    public function testUpsert()
    {
        $this->connection->expects($this->once())
            ->method('insert');

        $builder = new Builder($this->connection);

        $builder->upsert([ 'uniq' => 1, 'non_uniq' => 1 ], 'uniq');
    }

    public function testGroupByGroupingSets()
    {
        $builder = new Builder($this->connection);
        $builder->from = 'some_table';

        $builder->groupByGroupingSets('aaa', 'bbb', [ 'ccc', 'ddd' ], null);

        $expected = 'select * from "some_table" '
            . 'group by grouping sets ( ("aaa"), ("bbb"), ("ccc", "ddd"), () )';
        $this->assertEquals($expected, $builder->toSql());
    }

    public function testGroupByRollup()
    {
        $builder = new Builder($this->connection);
        $builder->from = 'some_table';

        $builder->groupByRollup('aaa', 'bbb');

        $expected = 'select * from "some_table" '
            . 'group by rollup ( "aaa", "bbb" )';
        $this->assertEquals($expected, $builder->toSql());
    }

    public function testGroupByCube()
    {
        $builder = new Builder($this->connection);
        $builder->from = 'some_table';

        $builder->groupByCube('aaa', 'bbb');

        $expected = 'select * from "some_table" '
            . 'group by cube ( "aaa", "bbb" )';
        $this->assertEquals($expected, $builder->toSql());
    }

    public function testComplexGroupBy()
    {
        $builder = new Builder($this->connection);
        $builder->from = 'some_table';

        $builder->groupBy('aaa', 'bbb')
            ->groupByGroupingSets('ccc', 'ddd', [ 'eee', 'fff' ])
            ->groupByCube('ggg', 'hhh');

        $expected = 'select * from "some_table" '
            . 'group by '
            . '"aaa", "bbb", '
            . 'grouping sets ( ("ccc"), ("ddd"), ("eee", "fff") ), '
            . 'cube ( "ggg", "hhh" )';
        $this->assertEquals($expected, $builder->toSql());
    }
}
