<?php

namespace YuryKabanov\Database\Schema\Grammars;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Grammars\PostgresGrammar as BasePostgresGrammar;
use Illuminate\Support\Fluent;

class PostgresGrammar extends BasePostgresGrammar
{
    /**
     * Index methods supported by PostgreSQL
     */
    const SUPPORTED_INDEX_METHODS = [ 'btree', 'hash', 'gin', 'gist', 'spgist', 'brin' ];

    /**
     * @param Blueprint $blueprint
     * @param Fluent $command
     * @return string
     */
    public function compileIndex(Blueprint $blueprint, Fluent $command)
    {
        $columns = $this->columnize($command->columns);

        $index = $this->wrap($command->index);

        // determine should index be created concurrently
        $concurrently = !empty($command->options['concurrently']) ? 'concurrently' : '';

        // check for any supported method and use it or default btree method
        $method = in_array($command->method, static::SUPPORTED_INDEX_METHODS) ? $command->method : 'btree';

        // uniqueness
        $unique = !empty($command->options['unique']) ? 'unique' : '';

        return join(" ", array_filter([
            "create", $unique, "index", $concurrently, $index,       // CREATE [UNIQUE] INDEX [CONCURRENTLY] name
            "on", $this->wrapTable($blueprint),                      // ON table
            "using", $method,                                        // USING method
            "({$columns})"                                           // columns
        ]));
    }
}
