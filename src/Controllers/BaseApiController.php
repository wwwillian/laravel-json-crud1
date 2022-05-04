<?php

namespace Wwwillian\JsonCrud\Controllers;

use Illuminate\Http\Request;
use Wwwillian\JsonCrud\Controllers\BaseController;

class BaseApiController extends BaseController
{
    /**
     * List all models.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        return $this->service->all($request->page_size);
    }

    /**
     * Show a model.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        return $this->service->find($id)->toArray();
    }

    /**
     * Store a new model.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        return $this->service->create($request);
    }

    /**
     * Update the given model.
     *
     * @param  Request  $request
     * @param  string  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        return $this->service->update($request, $id);
    }
}
