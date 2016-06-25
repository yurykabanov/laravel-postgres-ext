<?php

namespace YuryKabanov\Database;

use Illuminate\Database\PostgresConnection as BasePostgresConnection;

use YuryKabanov\Database\Schema\PostgresBuilder;
use YuryKabanov\Database\Query\Grammars\PostgresGrammar as QueryGrammar;
use YuryKabanov\Database\Schema\Grammars\PostgresGrammar as SchemaGrammar;

class PostgresConnection extends BasePostgresConnection {
    /**
     * {@inheritdoc}
     */
    public function getSchemaBuilder() {
        if (is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }

        return new PostgresBuilder($this);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultQueryGrammar() {
        return $this->withTablePrefix(new QueryGrammar);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultSchemaGrammar() {
        return $this->withTablePrefix(new SchemaGrammar);
    }
}
