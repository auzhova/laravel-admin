<?php

namespace App\Admin;

use Closure;
use Encore\Admin\Form;
use Encore\Admin\Form\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Spatie\EloquentSortable\Sortable;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Form.
 */
class AdminForm extends Form
{

    /**
     * Create a new form instance.
     *
     * @param $model
     * @param \Closure $callback
     */
    public function __construct($model, Closure $callback = null)
    {
        parent::__construct($model, $callback);
    }

    /**
     * Prepare input data for update.
     *
     * @param array $updates
     * @param bool  $oneToOneRelation If column is one-to-one relation.
     *
     * @return array
     */
    protected function prepareUpdate(array $updates, $oneToOneRelation = false): array
    {
        $prepared = [];

        /** @var Field $field */
        foreach ($this->fields() as $field) {
            $columns = $field->column();

            // If column not in input array data, then continue.
            if (!Arr::has($updates, $columns)) {
                continue;
            }

            if ($this->isInvalidColumn($columns, $oneToOneRelation || $field->isJsonType)) {
                continue;
            }

            $value = $this->getDataByColumn($updates, $columns);

            $value = $field->prepare($value);

            if (is_array($columns)) {
                foreach ($columns as $name => $column) {
                    Arr::set($prepared, $column, $value[$name]);
                }
            } elseif (is_string($columns)) {
                if (strripos( $columns, '.' )) {
                    $array = explode('.' , $columns);
                    $oldValues = $this->model()[$array[0]];
                    Arr::set($oldValues, str_replace($array[0].'.', '', $columns), $value);
                    if (Arr::get($prepared, $array[0])) {
                        Arr::set($prepared, $columns, $value);
                    } else {
                        Arr::set($prepared, $array[0], $oldValues);
                    }
                } else {
                    Arr::set($prepared, $columns, $value);
                }
            }

        }

        return $prepared;
    }

    /**
     * Prepare input data for insert.
     *
     * @param $inserts
     *
     * @return array
     */
    protected function prepareInsert($inserts): array
    {
        if ($this->isHasOneRelation($inserts)) {
            $inserts = Arr::dot($inserts);
        }

        foreach ($inserts as $column => $value) {
            if (($field = $this->getFieldByColumn($column)) === null) {
                unset($inserts[$column]);
                continue;
            }

            $inserts[$column] = $field->prepare($value);
        }
dd($inserts);
        $prepared = [];

        foreach ($inserts as $key => $value) {
            Arr::set($prepared, $key, $value);
        }

        return $prepared;
    }

    /**
     * Find field object by column.
     *
     * @param $column
     *
     * @return mixed
     */
    protected function getFieldByColumn($column)
    {
        return $this->fields()->first(
            function (Field $field) use ($column) {
                if (strripos( $field->column(), '.' )) {
                    $array = explode('.' , $field->column());
                    dump($array,$field->column(),$column,$array[0],$column,$array[0] == $column);
                    return $array[0] == $column;
                }
                if (is_array($field->column())) {
                    return in_array($column, $field->column());
                }

                return $field->column() == $column;
            }
        );
    }
}
