<?php

namespace ConnectMalves\JsonCrud\Services\Contracts;

use Illuminate\Http\Request;

interface IService {
    // public function create(Request $request);
    public function update(Request $request, $id);
    public function all($size);
    public function find($id);
    public function requestData(Request $request);
    public function findOrFail($id);
    public function findOneBy(Request $request);
    public function findOneByColumns(Request $request);
}
