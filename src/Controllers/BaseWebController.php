<?php

namespace ConnectMalves\JsonCrud\Controllers;

use ConnectMalves\JsonCrud\Controllers\BaseController;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;

class BaseWebController extends BaseController
{
    /**
     * The constructor.
     */
    public function __construct()
    {
        parent::__construct();
        View::share('attributes', $this->service->frontend());
        View::share('views', $this->service->views());
        View::share('routePrefix', $this->routePrefix());
        View::share('entity', $this->entity());
    }

    /**
     * Show a model.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        return view(config('jsoncrud.view', 'jcrud') . '::cruds.show.default', ['model' => $this->service->findOrFail($id)]);
    }

    /**
     * Edit the given model.
     *
     * @param  Request  $request
     * @return View
     */
    public function edit(Request $request, $id)
    {
        return view(config('jsoncrud.view', 'jcrud') . '::cruds.edit.default')
            ->with('model', $this->service->findOrFail($id));
    }

    /**
     * Show model creation view.
     *
     * @param  Request  $request
     * @return View
     */
    public function create(Request $request)
    {
        // View for create model
        return view(config('jsoncrud.view', 'jcrud') . '::cruds.create.default');
    }

    /**
     * Store a new model.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->service->create($request);
        return back()->with('success', trans('messages.create_success', ['entity' => trans('entities.'. strtolower($this->entity()) . '.singular')]));
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
        $this->service->update($request, $id);
        return back()->with('success', trans('messages.edit_success', ['entity' => trans('entities.'. strtolower($this->entity()) . '.singular')]));
    }

    /**
     * Displays datatables front end view
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view(config('jsoncrud.view', 'jcrud') . '::cruds.index.default');
    }

    /**
     * Destroy the given model.
     *
     * @param  string  $id
     * @return Response
     */
    public function destroy($id)
    {
        $this->service->delete($id);
        return back()->with('success', trans('messages.delete_success', ['entity' => trans('entities.'. strtolower($this->entity()) . '.singular')]));
    }
}
