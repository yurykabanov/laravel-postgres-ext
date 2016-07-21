<?php

namespace YuryKabanov\Database\Schema;

use Illuminate\Database\Schema\PostgresBuilder as BasePostgresBuilder;

class PostgresBuilder extends BasePostgresBuilder
{
    /**
     * {@inheritdoc}
     *
     * @return Blueprint
     */
    protected function createBlueprint($table, \Closure $callback = null)
    {
        return new Blueprint($table, $callback);
    }

    /**
     * Creates view as given select statement
     *
     * @param string $view
     * @param string $select
     * @param bool $materialize
     */
    public function createView($view, $select, $materialize = false) {
        $blueprint = $this->createBlueprint($view);

        $blueprint->createView($select, $materialize);

        $this->build($blueprint);
    }

    /**
     * Drops view
     *
     * @param string $view
     */
    public function dropView($view) {
        $blueprint = $this->createBlueprint($view);

        $blueprint->dropView();

        $this->build($blueprint);
    }
}
