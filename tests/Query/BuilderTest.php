<?php

namespace YuryKabanov\Database\Query;

use PHPUnit\Framework\TestCase;

use YuryKabanov\Database\PostgresConnection;

class BuilderTest extends TestCase {
    public function testUpsert() {
        $grammar = new Grammars\PostgresGrammar;

        $connection = $this->getMockBuilder(PostgresConnection::class)
            ->setConstructorArgs([ null ])
            ->setMethods([ 'insert' ])
            ->getMock();

        $connection->expects($this->once())
            ->method('insert');

        $builder = new Builder($connection);

        $builder->upsert([ 'uniq' => 1, 'non_uniq' => 1 ], 'uniq');
    }
}
