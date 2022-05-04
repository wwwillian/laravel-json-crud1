<?php

namespace ConnectMalves\JsonCrud\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait JsonEloquentConfigurator
{
    /**
     * The array of rules when a model is stored.
     *
     * @var array
     */
    protected $storeRules = [];

    /**
     * The db type enum attributes.
     *
     * @var array
     */
    protected $enums = [];

    /**
     * The translatable attributes.
     *
     * @var array
     */
    protected $translatable = [];

    /**
     * The array of rules when a model is updated.
     *
     * @var array
     */
    protected $updateRules = [];

    /**
     * The array of rules that will be validated when a model is stored or updated.
     *
     * @var array
     */
    protected $defaultRules = [];

    /**
     * The array of attributes that has tags.
     *
     * @var array
     */
    protected $tags = [];

    /**
     * The attributes that should be mutated to times.
     *
     * @var array
     */
    protected $times = [];

    /**
     * The attributes that should be mutated to money.
     *
     * @var array
     */
    protected $moneyAttributes = [];

    /**
     * The attributes that should be sanitized.
     *
     * @var array
     */
    protected $sanitizers = [];

    public function setAttributeRelation($attribute)
    {
        $visibleRelation = preg_replace('/\.\w+$/', '', $attribute);
        $visibleRelation = preg_replace('/\.\*/', '', $visibleRelation);
        $visibleRelation = preg_replace('/\.\d/', '', $visibleRelation);

        $relation = explode(".", $attribute)[0];

        if (method_exists($this, $relation)) {

            if (!in_array($visibleRelation, $this->visibleRelations)) {
                $this->visibleRelations[] = Str::camel($visibleRelation);
            }

            if (!in_array($relation, $this->visible)) {
                $this->visible[] = Str::camel($relation);
            }
            if (!in_array($relation, $this->availableRelations)) {
                $this->availableRelations[] = Str::camel($relation);
            }
        }
    }

    public function setAttributeDefaultValue($attribute, $value)
    {
        if (!is_null($value)) {
            $this->attributes[$attribute] = $value;
        } else {
            unset($this->attributes[$attribute]);
        }
    }

    public function setAttributeSanitizers($attribute, $value)
    {
        if (!is_null($value)) {
            $this->sanitizers[$attribute] = $value;
        }
    }

    public function setAttributeIsUnique($attribute, $isUnique)
    {
        if ($isUnique) {
            $array  = explode('.', $attribute);
            $column = array_pop($array);
            $table  = $this->table;
            if (count($array) > 0) {
                $relationName = array_pop($array);
                if ($relationName == "*") {
                    $relationName = array_pop($array);
                }
                $relation = $this;
                foreach ($array as $item) {
                    $relation = $relation->{$item}()->getRelated();
                }
                if (method_exists($relation, $relationName)) {
                    $table = $relation->{$relationName}()->getRelated()->table;
                }
            }
            $this->defaultRules[$attribute] = ['unique:' . $table . ',' . $column];
        }
    }

    public function setAttributeRequirement($attribute, $isRequired)
    {
        if ($isRequired) {
            $this->storeRules[$attribute][] = 'required';
        } else {
            $this->storeRules[$attribute][] = 'nullable';
            unset($this->storeRules[$attribute]['required']);
        }
    }

    public function setAttributeHasTag($attribute, $hasTags)
    {
        if ($hasTags) {
            $this->tags[] = $attribute;
        } else {
            unset($this->tags[$attribute]);
        }
    }

    public function setAttributeIsTranslatable($attribute, $isTranslatable)
    {
        if ($isTranslatable) {
            $this->translatable[] = $attribute;
        } else {
            unset($this->translatable[$attribute]);
        }
    }

    public function setAttributeIsBoolean($attribute, $isBoolean)
    {
        if ($isBoolean) {
            $array = explode('.', $attribute);
            if (count($array) == 1 && in_array($attribute, $this->fillable)) {
                $this->casts[$attribute] = 'boolean';
            }
        }
    }

    public function setAttributeIsMoneyType($attribute, $isMoney)
    {
        if ($isMoney) {
            $this->moneyAttributes[]      = $attribute;
            $this->sanitizers[$attribute] = 'currency';
        }
    }

    protected function getDefaultAccessorDateFormat()
    {
        return $this->defaultDateFormat ? $this->defaultDateFormat : 'd/m/Y';
    }

    public function setAttributeTypes($attribute, $dbSettings)
    {
        $array = explode('.', $attribute);

        switch ($dbSettings['type']) {
            case "char":
                if (count($array) == 1) {
                    $this->casts[$attribute] = 'string';
                }
                $this->defaultRules[$attribute][] = !isset($dbSettings['size']) || $dbSettings['size'] > 255 ? 'max:.255' : 'max:' . $dbSettings['size'];
                $this->defaultRules[$attribute][] = 'alpha';
                break;
            case "varchar":
                if (count($array) == 1) {
                    $this->casts[$attribute] = 'string';
                }
                if (isset($dbSettings['size'])) {
                    $this->defaultRules[$attribute][] = 'max:' . $dbSettings['size'];
                }
                break;
            case "tinytext":
                if (count($array) == 1) {
                    $this->casts[$attribute] = 'string';
                }
                $this->defaultRules[$attribute][] = 'max:255';
                break;
            case "text":
                if (count($array) == 1) {
                    $this->casts[$attribute] = 'string';
                }
                $this->defaultRules[$attribute][] = 'max:65535';
                break;
            case "enum":
                if (count($array) == 1) {
                    $this->casts[$attribute] = 'alpha_num';
                }

                $this->enums[]                    = $attribute;
                $this->defaultRules[$attribute][] = 'in:' . implode(",", $dbSettings['enum']);
                break;
            case "set":
                if (count($array) == 1) {
                    $this->casts[$attribute] = 'alpha_num';
                }
                break;
            case "time":
                if (count($array) == 1) {
                    $this->times[] = $attribute;
                }
                break;
            case "year":
                if (count($array) == 1) {
                    $this->casts[$attribute] = 'alpha_num';
                }
                $this->defaultRules[$attribute][] = 'between:1,12';
                $this->defaultRules[$attribute][] = 'integer';
                break;
            case "tinyint":
                if (count($array) == 1) {
                    $this->casts[$attribute] = 'integer';
                }
                if (!isset($dbSettings['size']) || $dbSettings['size'] > 255) {
                    $this->defaultRules[$attribute][] = 'digits_between:1,255';
                    $this->defaultRules[$attribute][] = 'integer';
                } else if ($dbSettings['size'] != 1) {
                    $this->defaultRules[$attribute][] = 'digits_between:1,' . $dbSettings['size'];
                    $this->defaultRules[$attribute][] = 'integer';
                }
                break;
            case "smallint":
                if (count($array) == 1) {
                    $this->casts[$attribute] = 'integer';
                }
                $this->defaultRules[$attribute][] = !isset($dbSettings['size']) || $dbSettings['size'] > 65535 ? 'max:65535' : 'digits_between:1,' . $dbSettings['size'];
                $this->defaultRules[$attribute][] = 'integer';
                break;
            case "mediumint":
                if (count($array) == 1) {
                    $this->casts[$attribute] = 'integer';
                }
                $this->defaultRules[$attribute][] = !isset($dbSettings['size']) || $dbSettings['size'] > 16777215 ? 'max:16777215' : 'digits_between:1,' . $dbSettings['size'];
                $this->defaultRules[$attribute][] = 'integer';
                break;
            case "int":
                if (count($array) == 1) {
                    $this->casts[$attribute] = 'integer';
                }
                $this->defaultRules[$attribute][] = !isset($dbSettings['size']) || $dbSettings['size'] > 4294967295 ? 'max:4294967295' : 'digits_between:1,' . $dbSettings['size'];
                $this->defaultRules[$attribute][] = 'integer';
                break;
            case "bigint":
                if (count($array) == 1) {
                    $this->casts[$attribute] = 'integer';
                }
                $this->defaultRules[$attribute][] = !isset($dbSettings['size']) || $dbSettings['size'] > 18446744073709551615 ? 'max:18446744073709551615' : 'digits_between:1,' . $dbSettings['size'];
                $this->defaultRules[$attribute][] = 'integer';
                break;
            case "float":
                if (count($array) == 1) {
                    $this->casts[$attribute] = 'float';
                }
                break;
            case "double":
                if (count($array) == 1) {
                    $this->casts[$attribute] = 'double';
                }
                break;
            case "decimal":
                if (count($array) == 1) {
                    $this->casts[$attribute] = 'real';
                }
                break;
            case "date":
                if (count($array) == 1) {
                    $this->casts[$attribute] = 'date:' . $this->getDefaultAccessorDateFormat();
                }
                $this->defaultRules[$attribute][] = 'date';
                break;
            case "timestamp":
                if (count($array) == 1) {
                    $this->casts[$attribute] = 'timestamp';
                }
                $this->defaultRules[$attribute][] = 'date';
                break;
            case "foreign_key":
                $this->defaultRules[$attribute][] = 'integer';
                break;
        }
        $this->updateRules[$attribute][] = 'nullable';
    }

    public function getMoneyAttributes()
    {
        return $this->moneyAttributes;
    }

    public function getEnums()
    {
        return $this->enums;
    }

    public function getAttributeNameByType($attribute)
    {
        if (!Arr::has($this->frontend(), $attribute)) {
            return null;
        }
        return $this->frontend()[$attribute]['frontend']['type'];
    }

    public function getAttributesByType($type)
    {
        return array_keys(Arr::where($this->frontend(), function ($value, $key) use ($type) {
            return $value['frontend']['type'] == $type;
        }));
    }

    public function getTranslatable($type)
    {
        return $this->translatable;
    }

    public function getSanitizers()
    {
        return $this->sanitizers;
    }

    public function setAttributeFillability($attribute, $isfillable)
    {
        if ($isfillable) {
            $this->fillable[] = $attribute;
        } else {
            unset($this->fillable[$attribute]);
        }
    }

    public function setAttributeVisibility($attribute, $isVisible)
    {
        if ($isVisible) {
            $this->visible[] = $attribute;
            unset($this->hidden[$attribute]);
        } else {
            unset($this->visible[$attribute]);
            $this->hidden[] = $attribute;
        }
    }

    public function setValidationRules($attribute, $rules)
    {
        $this->storeRules[$attribute]  = array_merge($this->storeRules[$attribute] ?? [], $rules['store'] ?? []);
        $this->updateRules[$attribute] = array_merge($this->updateRules[$attribute] ?? [], $rules['update'] ?? []);
    }

    public function removeValidationRules($attribute)
    {
        unset($this->storeRules[$attribute]);
        unset($this->updateRules[$attribute]);
    }

    public function setTimestamps($flag)
    {
        $this->timestamps = $flag;
    }

    public function setTablename($table)
    {
        if (!is_null($table) && $table != "") {
            $this->table = $table;
        }
    }

    public function updateRules()
    {
        $keysToForget = array_keys(Arr::where($this->defaultRules, function ($value, $key) {
            return strpos(implode(",", Arr::wrap($value)), "unique") !== false;
        }));
        $defaultRules = $this->defaultRules;
        foreach ($keysToForget as $key) {
            Arr::forget($defaultRules, $key);
        }
        return array_merge_recursive($defaultRules, $this->updateRules);
    }

    public function storeRules()
    {
        return array_merge_recursive($this->defaultRules, $this->storeRules);
    }

    public function casts()
    {
        return $this->casts;
    }

    public function tags()
    {
        return $this->tags;
    }
}
