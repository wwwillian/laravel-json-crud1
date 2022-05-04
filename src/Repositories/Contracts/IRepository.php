<?php

namespace ConnectMalves\JsonCrud\Repositories\Contracts;

interface IRepository
{
    public function find($id);
    public function findAll();
    public function delete($id);
    public function storeRules();
    public function updateRules();
    public function sanitizers();
    public function findOrFail($id);
    public function paginate($pages);
    public function fill(array $array);
    public function create(array $data);
    public function update(array $data, $id);
    public function relationType($relation);
    public function instance($withRelations);
    public function newInstance();
    public function firstOrCreate(array $data);
    public function findOneBy(array $criteria);
    public function __call($method, $arguments);
    public function fillRelation($relation, $data);
    public function findBy(array $criteria, array $orderBy = [], $limit = null, $offset = null);
}
