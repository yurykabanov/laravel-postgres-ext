<?php

namespace YuryKabanov\Database\Query\Grammars;

use Illuminate\Database\Query\Grammars\PostgresGrammar as BasePostgresGrammar;
use Illuminate\Database\Query\Builder as BaseBuilder;

use YuryKabanov\Database\Query\Builder;

class PostgresGrammar extends BasePostgresGrammar
{
    /**
     * Jsonb operators that require function wrapping
     *
     * TODO: operator '?|' also fits for points and lines
     *
     * @var array
     */
    protected $jsonbOperators = [
        '?' => 'jsonb_exists',
        '?|' => 'jsonb_exists_any',
        '?&' => 'jsonb_exists_all'
    ];

    /**
     * Compile an upsert statement into SQL.
     *
     * @param Builder $query
     * @param array $values
     * @param string $unique
     * @return string
     */
    public function compileUpsert(Builder $query, array $values, $unique)
    {
        $insert = $this->compileInsert($query, $values);

        if (! is_array(reset($values))) {
            $values = [$values];
        }
        $keys = array_keys(reset($values));

        // excluded fields are all fields except $unique one that will be updated
        // also created_at should be excluded since record already exists
        $excluded = array_filter($keys, function ($e) use ($unique) {
            return $e != $unique && $e != 'created_at';
        });

        $update = join(', ', array_map(function ($e) { return "\"$e\" = \"excluded\".\"$e\""; }, $excluded));

        return "$insert on conflict ($unique) do update set $update";
    }

    /**
     * {@inheritdoc}
     */
    protected function whereBasic(BaseBuilder $query, $where)
    {
        if (in_array($where['operator'], array_keys($this->jsonbOperators))) {
            return $this->whereJsonbOperators($where);
        }

        return parent::whereBasic($query, $where);
    }

    /**
     * Compile where clause wrapping jsonb operators with appropriate functions
     *
     * @param $where
     * @return string
     */
    protected function whereJsonbOperators($where)
    {
        $value = $this->parameter($where['value']);

        $func = $this->jsonbOperators[$where['operator']];

        return "$func(" . $this->wrap($where['column']) . ', ' . $value . ')';
    }

    /**
     * Compile "grouping sets" expression
     *
     * @param array $groups
     * @return string
     */
    public function compileGroupingSets(array $groups)
    {
        $args = array_map(function ($group) {
            return '(' . join(', ', $this->wrapColumns($group)) . ')';
        }, $groups);

        return 'grouping sets ( ' . join(', ', $args) . ' )';
    }
    /**
     * Compile "rollup" expression
     *
     * @param array $groups
     * @return string
     */

    public function compileRollup(array $groups)
    {
        $args = $this->wrapColumns($groups);
        return 'rollup ( ' . join(', ', $args) . ' )';
    }
    /**
     * Compile "cube" expression
     *
     * @param array $groups
     * @return string
     */
    public function compileCube(array $groups)
    {
        $args = $this->wrapColumns($groups);
        return 'cube ( ' . join(', ', $args) . ' )';
    }

    /**
     * Wraps array of columns
     *
     * @param array $columns
     * @return array
     */
    protected function wrapColumns($columns)
    {
        return array_map(function ($e) {
            return $this->wrap($e);
        }, (array) $columns);
    }
}
