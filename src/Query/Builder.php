<?php

namespace YuryKabanov\Database\Query;

use Illuminate\Database\Query\Builder as BaseBuilder;

class Builder extends BaseBuilder
{
    /**
     * @var Grammars\PostgresGrammar The database query grammar instance.
     */
    protected $grammar;

    /**
     * Performs UPSERT statement against selected database
     *
     * @param array $values
     * @param string $unique
     *
     * @return bool
     */
    public function upsert(array $values, $unique)
    {
        if (empty($values)) {
            return true;
        }

        if (! is_array(reset($values))) {
            $values = [$values];
        } else {
            foreach ($values as $key => $value) {
                ksort($value);
                $values[$key] = $value;
            }
        }

        $bindings = [];

        foreach ($values as $record) {
            foreach ($record as $value) {
                $bindings[] = $value;
            }
        }

        $sql = $this->grammar->compileUpsert($this, $values, $unique);

        $bindings = $this->cleanBindings($bindings);

        // we can use insert since upsert is customized insert
        return $this->connection->insert($sql, $bindings);
    }


    /**
     * Add a "group by" clause with "grouping sets" to the query.
     *
     * @param array ...$args
     * @return $this
     */
    public function groupByGroupingSets(...$args)
    {
        $expr = $this->grammar->compileGroupingSets($args);

        $this->groups[] = $this->connection->raw($expr);

        return $this;
    }

    /**
     * Add a "group by" clause with "rollup" to the query.
     *
     * @param array ...$args
     * @return $this
     */
    public function groupByRollup(...$args)
    {
        $expr = $this->grammar->compileRollup($args);

        $this->groups[] = $this->connection->raw($expr);

        return $this;
    }

    /**
     * Add a "group by" clause with "cube" to the query.
     *
     * @param array ...$args
     * @return $this
     */
    public function groupByCube(...$args)
    {
        $expr = $this->grammar->compileCube($args);

        $this->groups[] = $this->connection->raw($expr);

        return $this;
    }
}
