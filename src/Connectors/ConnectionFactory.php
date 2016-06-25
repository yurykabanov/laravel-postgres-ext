<?php

namespace YuryKabanov\Database\Connectors;

use YuryKabanov\Database\PostgresConnection;
use Illuminate\Database\Connectors\ConnectionFactory as BaseConnectionFactory;

class ConnectionFactory extends BaseConnectionFactory {
    /**
     * {@inheritdoc}
     */
    protected function createConnection($driver, $connection, $database, $prefix = '', array $config = []) {
        if ($this->container->bound($key = "db.connection.{$driver}")) {
            return $this->container->make($key, [$connection, $database, $prefix, $config]);
        }

        // Override pgsql connection
        if ($driver == 'pgsql') {
            return new PostgresConnection($connection, $database, $prefix, $config);
        }

        // use default behavior otherwise
        return parent::createConnection($driver, $connection, $database, $prefix, $config);
    }
}
