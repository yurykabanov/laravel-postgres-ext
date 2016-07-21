<?php

namespace YuryKabanov\Database\Schema;

use Illuminate\Database\Schema\Blueprint as BaseBlueprint;

class Blueprint extends BaseBlueprint
{
    /**
     * @param array|string $columns
     * @param null $name
     * @param string $method
     * @param array $options
     * @return \Illuminate\Support\Fluent
     */
    public function index($columns, $name = null, $method = 'btree', array $options = [])
    {
        return $this->indexCommand('index', $columns, $name, $method, $options);
    }

    public function unique($columns, $name = null, $method = 'btree', array $options = [])
    {
        return $this->indexCommand('unique', $columns, $name, $method, $options);
    }


    /**
     * @param string $type
     * @param array|string $columns
     * @param string $index
     * @param string $method
     * @param array $options
     * @return \Illuminate\Support\Fluent
     */
    protected function indexCommand($type, $columns, $index, $method = null, $options = null)
    {
        $columns = (array) $columns;

        // If no name was specified for this index, we will create one using a basic
        // convention of the table name, followed by the columns, followed by an
        // index type, such as primary or index, which makes the index unique.
        if (is_null($index)) {
            $index = $this->createIndexName($type, $columns);
        }

        return $this->addCommand($type, compact('index', 'columns', 'method', 'options'));
    }
}
