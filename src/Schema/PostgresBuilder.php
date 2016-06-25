<?php

namespace YuryKabanov\Database\Schema;

use Illuminate\Database\Schema\PostgresBuilder as BasePostgresBuilder;

class PostgresBuilder extends BasePostgresBuilder {
    /**
     * {@inheritdoc}
     */
    protected function createBlueprint($table, \Closure $callback = null) {
        return new Blueprint($table, $callback);
    }
}
