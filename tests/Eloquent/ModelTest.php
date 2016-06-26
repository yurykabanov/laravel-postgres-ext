<?php

namespace YuryKabanov\Database\Eloquent;

use PHPUnit\Framework\TestCase;

use Illuminate\Database\ConnectionResolverInterface;
use YuryKabanov\Database\PostgresConnection;
use YuryKabanov\Database\Query\Builder;

class ModelTest extends TestCase {
    public function testQueryBuilder() {
        $connection = $this->getMockBuilder(PostgresConnection::class)
            ->setConstructorArgs([ null ])
            ->getMock();

        $resolver = $this->getMockBuilder(ConnectionResolverInterface::class)
            ->getMock();
        $resolver->method('connection')->willReturn($connection);

        $model = $this->getMockForAbstractClass(Model::class);
        Model::setConnectionResolver($resolver);

        $this->assertInstanceOf(Builder::class, $model->newQuery()->getQuery());
    }
}
