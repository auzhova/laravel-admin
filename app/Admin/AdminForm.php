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
                    $this->model()->update([$columns => $value]);
                } else {
                    Arr::set($prepared, $columns, $value);
                }
            }

        }

        return $prepared;
    }
}
