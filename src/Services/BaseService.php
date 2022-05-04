<?php

namespace Wwwillian\JsonCrud\Services;

use Wwwillian\JsonCrud\Services\Contracts\IService;
use Illuminate\Http\Request;
use Rees\Sanitizer\Sanitizer;
use Validator;

class BaseService implements IService
{
    protected $repositoryClass;
    protected $repository;
    protected $sanitizer;
    protected $validator;

    public $relatedServices = [];

    public function __construct()
    {
        $this->sanitizer = new Sanitizer;
        $this->sanitizer->register('currency', function ($value) {
            return (double) preg_replace([0 => "/\./", 1 => "/,/"], [0 => "", 1 => "."], $value);
        });
        $this->sanitizer->register('digits', function ($value) {
            return !empty($value) ? (int) preg_replace("/\+|-/", '', filter_var($value, FILTER_SANITIZE_NUMBER_INT)) : "";
        });

        isset($this->repositoryClass) ? $this->repository = app()->make($this->repositoryClass): null;

        foreach ($this->relatedServices as $key => $value) {
            $this->relatedServices[$key] = app()->make($value);
        }
    }

    public function frontend()
    {
        return isset($this->repository) ? $this->repository->frontend() : null;
    }

    public function views()
    {
        return isset($this->repository) ? $this->repository->views() : null;
    }

    // public function create(Request $request)
    // {
    //     $inputs = $request->all();
    //     $filteredData = $this->sanitizer->sanitize($this->repository->sanitizers(), $inputs);
    //     $data = Validator::make($filteredData, $this->repository->storeRules())->validate();
    //     return $this->repository->create($data);
    // }

    public function requestData($data)
    {
        if (is_object($data) && get_class($data) == 'Illuminate\Http\Request') {
            $data = $data->all();
        }

        $sanitized = $this->sanitize($data);
        $data      = $this->validate($sanitized);
        return $data;
    }

    public function validate(array $data)
    {
        $this->validator = Validator::make($data, $this->repository->storeRules());
        return $this->validator->validate();
    }

    public function sanitize(array $data)
    {
        return $this->sanitizer->sanitize($this->repository->sanitizers(), $data);
    }

    public function update(Request $request, $id)
    {
        $inputs       = $request->all();
        $filteredData = $this->sanitizer->sanitize($this->repository->sanitizers(), $inputs);
        $filteredData = array_diff_assoc_recursive($filteredData, $this->repository->find($id)->toArray());
        $data         = Validator::make($filteredData, $this->repository->updateRules())->validate();
        return $this->repository->update($data, $id);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }

    public function findAll($withRelations = false)
    {
        return $this->repository->findAll($withRelations);
    }

    public function all($size)
    {
        return $this->repository->paginate($size);
    }

    public function findOneBy(Request $request)
    {
        return $this->repository->findOneBy($request->all());
    }

    public function findOneByColumns(Request $request)
    {
        return $this->repository->findOneByColumns($request->all());
    }

    public function instance()
    {
        return $this->repository->instance();
    }

    public function newInstance()
    {
        return $this->repository->newInstance();
    }

    public function tags()
    {
        return $this->repository->tags();
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function findOrFail($id)
    {
        return $this->repository->findOrFail($id);
    }
}
