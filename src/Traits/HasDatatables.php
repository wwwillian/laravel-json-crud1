<?php

namespace ConnectMalves\JsonCrud\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;

trait HasDatatables
{
    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function datatables()
    {
        $model = $this->service->instance();

        $raws = [
            'action',
        ];

        $datatables = Datatables::of($model->with($model->visibleRelations()))
            ->addColumn('action', function ($eloquent) {
                return view(config('jsoncrud.view', 'jcrud') . '::partials.datatables.actions', ['actions' => data_get($eloquent->views(), 'index.actions'), 'model' => $eloquent, 'routePrefix' => (explode('.', \Request::route()->getName()))[0]]);
            });
        foreach (request()->query('columns') as $column) {
            if ($column['name'] == 'action') {
                continue;
            }

            $datatables->editColumn($column['name'], function ($eloquent) use ($column) {
                $type      = $eloquent->getAttributeNameByType($column['name']);
                $attribute = strpos($column['name'], '.') !== false ? data_get($eloquent, Str::camel($column['name'])) : data_get($eloquent, $column['name']);

                if ($attribute == '' || is_null($attribute)) {
                    return $attribute != '' ? $attribute : trans('labels.none');
                }

                switch ($type) {
                    case 'phonenumber':
                        return to_phonenumber(data_get($eloquent, $column['name']));
                    case 'cpf':
                        return to_cpf(data_get($eloquent, $column['name']));
                    case 'cnpj':
                        return to_cnpj(data_get($eloquent, $column['name']));
                    case 'cep':
                        return to_cep(data_get($eloquent, $column['name']));
                    case 'cpfcnpj':
                        return to_cpfcnpj(data_get($eloquent, $column['name']));
                    case 'percent':
                        return to_percent(data_get($eloquent, $column['name']));
                    case 'money':
                        return to_currency(data_get($eloquent, $column['name']));
                    case 'select':
                        $type      = $eloquent->frontend()[$column['name']]['frontend']['options']['type'];
                        $translate = $eloquent->frontend()[$column['name']]['frontend']['options']['translate'] ?? false;
                        $options   = $eloquent->frontend()[$column['name']]['frontend']['options']['values'];
                        $option    = Arr::where($options, function ($value, $key) use ($eloquent, $column) {
                            return !is_null(data_get($eloquent, $column['name'])) && isset($value['value']) && $value['value'] == data_get($eloquent, $column['name']);
                        });

                        if (empty($option)) {
                            return trans('labels.none');
                        }
                        if ($type == 'query') {
                            return !is_null(data_get($eloquent, $column['name'])) ? data_get($option, '*.label') : trans('labels.none');
                        }

                        return !is_null(data_get($eloquent, $column['name'])) && $translate ? trans(data_get($option, '*.label')[0]) : data_get($option, '*.value');
                    case 'date':
                        $format = $eloquent->frontend()[$column['name']]['frontend']['format'] ?? 'd/m/Y';
                        return Carbon::create(data_get($eloquent, $column['name']))->format($format);
                    default:
                        return $attribute;
                }
            });
        }

        foreach ($this->service->tags() as $columnName) {
            $datatables->editColumn($columnName, function ($eloquent) use ($columnName) {
                return view(config('jsoncrud.view', 'jcrud') . '::partials.datatables.tags', ['value' => data_get($eloquent, $columnName), 'attribute' => $this->service->frontend()[$columnName]])->render();
            });
            $raws[] = $columnName;
        }

        $datatables->rawColumns($raws);

        $dateAttributes = collect($model->getCasts())->filter(function ($value, $key) {
            return strpos($value, 'date') !== false;
        });

        $dateAttributes->each(function ($value, $key) use ($datatables) {
            $datatables->filterColumn($key, function ($query) use ($key) {
                $searchColumn = collect(request()->input("columns.*"))->where('name', $key)->first();
                if (isset($searchColumn) && $searchColumn['searchable']) {
                    $dateType            = substr_count(request('search.value'), '/');
                    $dateSearchParameter = false;
                    switch ($dateType) {
                        case 0:
                            $dateSearchParameter = date_create_from_format('d', request('search.value'));
                            if ($dateSearchParameter != false) {
                                $query->whereDay($key, $dateSearchParameter->format('d'));
                            }
                            break;
                        case 1:
                            $dateSearchParameter = date_create_from_format('d/m', request('search.value'));
                            if ($dateSearchParameter != false) {
                                $query->whereDay($key, $dateSearchParameter->format('d'))->whereMonth($key, $dateSearchParameter->format('m'));
                            }
                            break;
                        case 2:
                            $dateSearchParameter = date_create_from_format('d/m/Y', request('search.value'));
                            if ($dateSearchParameter != false) {
                                $query->whereDate($key, $dateSearchParameter->format('Y-m-d'));
                            }
                            break;
                    }
                }

                return $query;
            });
        });

        return $datatables->make();
    }
}
