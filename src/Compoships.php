<?php

namespace Awobaz\Compoships;

use Awobaz\Compoships\Database\Eloquent\Concerns\HasRelationships;
use Awobaz\Compoships\Database\Grammar\Grammar;
use Awobaz\Compoships\Database\Grammar\MySqlGrammar;
use Awobaz\Compoships\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\Grammars\Grammar as BaseGrammar;
use Illuminate\Support\Str;

trait Compoships
{
    use HasRelationships;

    public function getAttribute($key)
    {
        if (is_array($key)) { //Check for multi-columns relationship
            return array_map(function ($k) {
                return parent::getAttribute($k);
            }, $key);
        }

        return parent::getAttribute($key);
    }

    public function qualifyColumn($column)
    {
        if (is_array($column)) { //Check for multi-column relationship
            return array_map(function ($c) {
                if (Str::contains($c, '.')) {
                    return $c;
                }

                return $this->getTable().'.'.$c;
            }, $column);
        }

        return parent::qualifyColumn($column);
    }

    /**
     * Configure Eloquent to use Compoships Query Builder.
     *
     * @return \Awobaz\Compoships\Database\Query\Builder|static
     */
    protected function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();
        $grammar = new MySqlGrammar();
        $grammar->setConnection($connection);

        return new QueryBuilder($connection, $grammar, $connection->getPostProcessor());
    }
}
