<?php

namespace ConnectMalves\JsonCrud\Repositories\Eloquent;

abstract class AbstractRepository
{
    /**
     * @var ConnnectMalves\JsonCrud\Models\BaseModel
     */
    protected $model;

    /**
     * The relation resolver callback.
     *
     * @var \Closure
     */
    protected static $getRelationsResolver;

    /**
     * Set the get relations resolver callback.
     *
     * @param  \Closure  $resolver
     * @return void
     */
    public static function getRelationsResolver(\Closure $resolver)
    {
        static::$getRelationsResolver = $resolver;
    }

    /**
     * Resolve the get relations or return the default value.
     *
     * @param  string  $pageName
     * @param  int  $default
     * @return int
     */
    public static function resolveGetRelations($relations = 'get_relations', $default = true)
    {
        if (isset(static::$getRelationsResolver)) {
            return call_user_func(static::$getRelationsResolver, $relations);
        }
        return $default;
    }

    public function newInstance()
    {
        return app()->make($this->modelClass);
    }

    public function instance($withRelations = false)
    {
        if ($withRelations == true) {
            $visibleRelations = static::resolveGetRelations() ? $this->model->visibleRelations() : [];
            return $this->model->load($visibleRelations);   
        }
        return $this->model;
    }

    public function storeRules()
    {
        return $this->model->storeRules();
    }

    public function updateRules()
    {
        return $this->model->updateRules();
    }

    public function find($id)
    {
        $visibleRelations = static::resolveGetRelations() ? $this->model->visibleRelations() : [];
        return $this->model->with($visibleRelations)->find($id);  
    }

    public function findOrFail($id)
    {
        $visibleRelations = static::resolveGetRelations() ? $this->model->visibleRelations() : [];
        return $this->model->with($visibleRelations)->findOrFail($id);  
    }

    public function findAll($withRelations = false)
    {
        if($withRelations) {
            $visibleRelations = static::resolveGetRelations() ? $this->model->visibleRelations() : [];
            return $this->model->with($visibleRelations)->get();  
        }
        return $this->model->all();
    }

    public function create(array $data)
    {
        $model = $this->instance(true);
        $model->fill($data)->save();
        return $model;
    }

    public function update(array $data, $id)
    {
        $model = $this->find($id);
        $model->update($data);
        return $model;
    }

    public function save()
    {
        return $this->model->instance()->save();
    }

    public function firstOrCreate(array $data)
    {
        return $this->model->firstOrCreate($data);
    }

    public function delete($id)
    {
        return $this->model->find($id)->delete();
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $model = $this->model;

        if (count($criteria) == 1) {
            foreach ($criteria as $c) {
                $model = $model->where($c[0], $c[1], $c[2]);
            }
        } elseif (count($criteria) > 1) {
            $model = $model->where($criteria[0], $criteria[1], $criteria[2]);
        }

        if (isset($orderBy) && count($orderBy) == 1) {
            foreach ($orderBy as $order) {
                $model = $model->orderBy($order[0], $order[1]);
            }
        } elseif (isset($orderBy) && count($orderBy > 1)) {
            $model = $model->orderBy($orderBy[0], $orderBy[1]);
        }

        if (isset($limit) && count($limit)) {
            $model = $model->take((int)$limit);
        }

        if (isset($offset) && count($offset)) {
            $model = $model->skip((int)$offset);
        }

        return $model->get();
    }

    public function findOneBy(array $criteria)
    {
        return $this->findBy($criteria)->first();
    }

    public function relationType($relation)
    {
        return $this->model->relationType($relation);
    }

    public function __call($method, $arguments)
    {
        if (substr($method, 0, 4) == 'find') {
            $by = substr($method, 6, strlen($method));
            $method = 'find';
        } else {
            if (substr($method, 0, 6) == 'findBy') {
                $by = substr($method, 6, strlen($method));
                $method = 'findBy';
            } else {
                if (substr($method, 0, 9) == 'findOneBy') {
                    $by = substr($method, 9, strlen($method));
                    $method = 'findOneBy';
                } else {
                    throw new \Exception(
                        "Undefined method '$method'. The method name must start with " .
                        "either findBy or findOneBy!"
                    );
                }
            }
        }
        if (!isset($arguments[0])) {
            throw new \Exception('You must have one argument');
        }

        $fieldName = lcfirst($by);

        return $this->$method([$fieldName, '=', $arguments[0]]);
    }

    public function paginate($pages)
    {
        return $this->model->paginate($pages);
    }

    public function fill(array $array)
    {
        return $this->model->fill($array);
    }

    public function fillRelation($relation, $data, $id = null)
    {
        $model = $this->model;
        if( isset($id) && $this->model->find($id)) {
            $model = $this->model->find($id);
        }
        $relationType = $this->model->relationType($relation);
        switch ($relationType) {
            case "BelongsTo":
                $model->{$relation}()->associate($data);
                break;
            case "BelongsToMany":
                $model->{$relation}()->attach($data);
                break;
            case "HasOne":
            case "HasMany":
                $model->{$relation}()->save($data);
                break;
        }
        return $model;
    }
}
