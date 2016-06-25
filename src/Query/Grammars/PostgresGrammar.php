<?php

namespace YuryKabanov\Database\Query\Grammars;

use Illuminate\Database\Query\Grammars\PostgresGrammar as BasePostgresGrammar;
use YuryKabanov\Database\Query\Builder;

class PostgresGrammar extends BasePostgresGrammar {
    /**
     * Compile an upsert statement into SQL.
     *
     * @param  \YuryKabanov\Database\Query\Builder  $query
     * @param  array  $values
     * @return string
     */
    public function compileUpsert(Builder $query, array $values, $unique) {
        $insert = $this->compileInsert($query, $values);

        $keys = array_keys(reset($values));

        // excluded fields are all fields except $unique one that will be updated
        // also created_at should be excluded since record already exists
        $excluded = array_filter($keys, function($e) use($unique) {
            return $e != $unique && $e != 'created_at';
        });

        $update = join(', ', array_map(function($e) { return "\"$e\" = \"excluded\".\"$e\""; }, $excluded));

        return "$insert on conflict ($unique) do update set $update";
    }
}
