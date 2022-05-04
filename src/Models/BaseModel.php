<?php

namespace ConnectMalves\JsonCrud\Models;

use ConnectMalves\JsonCrud\Traits\JsonEloquentConfigurator;
use ConnectMalves\JsonCrud\Events\BaseModelSaving;
use ConnectMalves\JsonCrud\Events\BaseModelFilling;
use ConnectMalves\JsonCrud\Traits\JsonParser;
use ConnectMalves\JsonCrud\Traits\ToArrayValues;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;

class BaseModel extends Model
{
    use JsonParser, JsonEloquentConfigurator, ToArrayValues;

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'saving' => BaseModelSaving::class
    ];

    /**
     * The storage format of the model's time columns.
     *
     * @var string
     */
    protected $timeFormat = 'H:i:s';

    /**
     * The format to be casted of the model's time columns.
     *
     * @var string
     */
    protected $timeCastFormat = 'g:i A';

    /**
     * The enum translation file.
     *
     * @var string
     */
    protected $enumTranslationFile = 'labels';

    /**
     * The array of fillable relations.
     *
     * @var array
     */
    protected $availableRelations = [];

    /**
     * The array of visible relations.
     *
     * @var array
     */
    protected $visibleRelations = [];

    /**
     * The array of relations that was filled and needs to be saved.
     *
     * @var array
     */
    protected $filledRelations = [];

    /**
     * The array of relations that needs to be saved.
     *
     * @var array
     */
    protected $saveRelations = [];

    /**
     * The array of guarded attributes.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The array of store rules.
     *
     * @var array
     */
    protected $storeRules = [];

    /**
     * The array of store rules.
     *
     * @var array
     */
    protected $updateRules = [];

    /**
     * The array of store rules.
     *
     * @var array
     */
    protected $defaultRules = [];

    /**
     * The fillable array.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * The attributes array.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * The auto increment attribute
     *
     * @var boolean
     */
    protected $autoIncrement = true;

    /**
     * The constructor.
     */
    public function __construct(array $attributes = array())
    {
        $this->configureModel();
        parent::__construct($attributes);
        // if ($this->keyType == 'int' && $this->incrementing && $this->autoIncrement) {
        //     $this->attributes[$this->primaryKey] = $this->nextAutoIncrementId();
        // }
    }

    /**
     * Configurate model by json.
     *
     * @var array
     */
    public function configureModel()
    {
        $this->deserializeJson();

        $this->setTimestamps($this->getJsonTimestamps());
        $this->setTableName($this->getJsonTablename());

        foreach ($this->getJsonAttributes() as $key => $attribute) {
            $this->setValidationRules($key, $this->getJsonAttributeValidationRules($key));

            if (str_contains($key, '.')) {
                $this->setAttributeRelation($key);
            }

            $this->setAttributeDefaultValue($key, $this->getJsonAttributeDefaultValue($key));
            $this->setAttributeFillability($key, $this->getJsonAttributeIsFillable($key));
            $this->setAttributeVisibility($key, $this->getJsonAttributeIsVisible($key));
            $this->setAttributeRequirement($key, $this->getJsonAttributeIsRequired($key));
            $this->setAttributeIsUnique($key, $this->getJsonAttributeIsUnique($key));
            $this->setAttributeTypes($key, $this->getJsonAttributeDb($key));
            $this->setAttributeIsBoolean($key, $this->getJsonAttributeIsBoolean($key));
            $this->setAttributeIsMoneyType($key, $this->getJsonAttributeIsMoney($key));
            $this->setAttributeHasTag($key, $this->getJsonAttributeHasTag($key));
            $this->setAttributeIsTranslatable($key, $this->getJsonAttributeIsTranslatable($key));
            $this->setAttributeSanitizers($key, $this->getJsonAttributeSanitizers($key));

        }
    }

    /**
     * Generate Hash to password fields.
     *
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        if(isset($value) && !empty($value)) {
            $this->attributes['password'] = Hash::make($value);
        }
    }

    public function toArray()
    {
        $array = array_merge($this->attributesToArray(), $this->relationsToArray());

        //hide all attributes that value is equal null
        foreach($array as $key => $value) {
            if($value === null) {
                unset($array[$key]);
            }
        }

        return $array;
    }

    public function visibleRelations()
    {
        return $this->visibleRelations;
    }

    public function getJsonFilename()
    {
        return $this->json;
    }

    public function getModule()
    {
        return $this->module ?: null;
    }

    public function table()
    {
        return $this->table;
    }

    public function getAllAttributes()
    {
        $attributes = $this->getAttributes();

        foreach($this->visibleRelations as $relation) {
            if(isset($this->{$relation})) {
                $attributes[$relation] = $this->{$relation}->getAttributes();
            }
        }

        return $attributes;
    }

    /**
     * Fill the model with an array of attributes.
     *
     * @param  array  $attributes
     * @return $this
     *
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     */
    public function fill(array $attributes)
    {
        event(new BaseModelFilling($this, $attributes));

        foreach($this->times as $time) {
            if(Arr::has($attributes, $time)) {
                $attributes[$time] = date_format(date_create_from_format($this->timeCastFormat, $attributes[$time]), $this->timeFormat);
            }
        }

        parent::fill($attributes);

        // $relations = Arr::where($attributes, function ($value, $key) {
        //     return is_array($value) && !is_integer($key);
        // });

        // foreach($relations as $relationName => $relation) {
        //     if($this->{$relationName}() != null) {
        //         $this->filledRelations[] = $relationName;
        //         $relationType = $this->relationType($relationName);
        //         $relationClass = $this->{$relationName}()->getRelated();
        //         $inverseRelationClassName = Str::camel((new \ReflectionClass($this))->getShortName());
        //         $related = new $relationClass();

        //         try {
        //             $related->{$inverseRelationClassName} = $this;
        //         }catch(Exception $e){
        //         }

        //         switch($relationType) {
        //             case "HasOne":
        //             case "BelongsTo":
        //                 $this->setRelation($relationName, $related);
        //                 $this->{$relationName}->fill($relation);
        //                 if($relationType == "BelongsTo") {
        //                     $foreignKey = $this->{$relationName}()->getForeignKeyName();
        //                     $this->attributes[$foreignKey] = $this->{$relationName}->id;
        //                     $this->fillable[] = $foreignKey;
        //                 }
        //             break;

        //             case "BelongsToMany":
        //             case "HasMany":
        //                 foreach($relation as $value) {
        //                     $related = new $relationClass();
        //                     $related->fill($value);
        //                     $this->{$relationName}->add($related);
        //                 }
        //             break;
        //         }
        //     }
        // }

        return $this;
    }

    /**
     * Overload model save.
     *
     */
    // public function save(array $options = [])
    // {
    //     DB::transaction(function () use ($options){
    //         // $relations = array_merge($this->saveRelations, $this->filledRelations);

    //         // foreach ($relations as $relation) {
    //         //     if (isset($this->{$relation})) {
    //         //         $relationType = $this->relationType($relation);
    //         //         switch($relationType) {
    //         //             case "BelongsTo":
    //         //                 $relationNotSaved = $this->{$relation};
    //         //                 unset($this->{$relation});
    //         //                 $relationNotSaved->save();
    //         //                 break;
    //         //         }
    //         //     }
    //         // }

    //         $allAttr = $this->getAttributes();
    //         foreach($this->getAttributes() as $key => $attr) {
    //             if(is_object($attr)) {
    //                 unset($this->attributes[$key]);
    //             }
    //         }

    //         parent::save($options);

    //         // foreach ($relations as $relation) {
    //         //     if (isset($this->{$relation})) {
    //         //         $relationType = $this->relationType($relation);
    //         //         $foreignKey = $this->{$relation}()->getForeignKeyName();
    //         //         switch($relationType) {
    //         //             case "HasOne":
    //         //                 $this->{$relation}->{$foreignKey} = $this->id;
    //         //                 $this->{$relation}->save();
    //         //                 break;
    //         //             case "HasMany":
    //         //             case "BelongsToMany":
    //         //                 $collection = $this->{$relation}->toArray();
    //         //                 $this->{$relation}()->saveMany($collection);
    //         //                 // foreach($this->{$relation} as $related) {
    //         //                 //     // $related->{$foreignKey} = $this->id;
    //         //                 //     $related->save();
    //         //                 // }
    //         //                 break;
    //         //             case "BelongsTo":
    //         //                     $relationAlreadySaved = $this->{$relation};
    //         //                     $this->{$relation}()->associate($relationAlreadySaved);
    //         //             break;
    //         //         }
    //         //     }
    //         // }
    //     }, 3);

    //     return $this->exists;
    // }

    public function relationType($relation)
    {
        return (new \ReflectionClass($this->{$relation}()))->getShortName();
    }
}
