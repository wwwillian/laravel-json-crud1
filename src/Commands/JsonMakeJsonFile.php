<?php

namespace Wwwillian\JsonCrud\Commands;

use Wwwillian\JsonCrud\Traits\JsonCommand;
use DB;
use Arr;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class JsonMakeJsonFile extends Command
{
    use JsonCommand;

    protected $arguments = [
        'name',
    ];

    protected $options = [
        'table',
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'json:make:jsonfile {name : The api controller name}
                                               {--T|table= : The table name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new json controller';

    /**
     * The tablename.
     *
     * @var string
     */
    protected $entityPlural;

    /**
     * The columns of table.
     *
     * @var array
     */
    protected $columns;

    /**
     * The foreign keys of table.
     *
     * @var array
     */
    protected $foreignKeys;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (config('jsoncrud.hasmodule')) {
            $this->signature .= "{--M|module=Core : The module name}";
        }
        parent::__construct();
        $this->filesystem = new Filesystem();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $config = $this->configurate();

        $this->entityPlural = $config->table ?: str_plural(snake_case($config->name));
        $this->columns = $this->getDBColumns();
        $this->foreignKeys = $this->getDBForeignKeys();
        
        $json     = $this->generateJson();
        $filename = $config->name . '.json';
        if (config('jsoncrud.hasmodule')) {
            $path = config('core.paths.modules') . '/' . $config->module . '/resources/json/';
        } else {
            $path = 'resources/json/';
        }

        $contents = isset($json) ? json_encode($json, JSON_PRETTY_PRINT) : "{\n    \"attributes\": {\n    }\n}\n";

        if (!is_dir($path)) {
            mkdir($path, 755, true);
        }

        file_put_contents($path . $filename, $contents);

        $this->info('Json File generated successfully');
    }

    protected function getDBColumns()
    {
        return json_decode(json_encode(DB::select("SHOW COLUMNS FROM " . $this->entityPlural)), true);
    }

    protected function getDBForeignKeys()
    {
        return json_decode(json_encode(DB::select("SELECT column_name AS 'foreign_key', referenced_table_name as 'referenced_table', referenced_column_name as 'referenced_column' FROM information_schema.key_column_usage WHERE referenced_table_name is not null and table_schema = '" . env('DB_DATABASE', 'iptv') . "' and table_name = '" . $this->entityPlural . "' ")), true);
    }

    protected function generateJsonAttributes()
    {
        $json = [];

        $foreign_keys = $this->foreignKeys;
        $columns      = $this->columns;

        //filter columns to remove unnecessary declarations on json
        $keys = Arr::except(data_get($foreign_keys, '*.foreign_key'), '*_by');

        // foreach ($foreign_keys as $column) {
        //     $generate = filter_var($this->ask('Generate related attributes from ' . $column['referenced_table'] . ' for auto fill ' . $this->entityPlural . '.' . $column['foreign_key'] . '?'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        //     if (!$generate) {
        //         unset($keys['foreign_key']);
        //         continue;
        //     }
        //     // $relatedColumns = $this->generateJsonAttributes();
        //     // foreach ($relatedColumns as $key => $value) {
        //     //     $relatedColumns[str_replace('_id', '', $column['foreign_key']) . '.' . $key] = $value;
        //     //     unset($relatedColumns[$key]);
        //     // }
        //     // $json = array_merge($json, $relatedColumns);
        // }

        foreach ($columns as $key => $column) {
            if (in_array($column['Field'], $keys) || str_contains($column['Field'], ['remember_token'])) {
                unset($columns[$key]);
            }
        }

        foreach ($columns as $column) {
            $name                                 = $column['Field'];
            $json[$name]['required']              = $column['Null'] == 'NO';
            $json[$name]['unique']                = $column['Key'] == 'UNI';
            $json[$name]['visible']               = !str_contains($name, ['password']) ? true : false;
            $json[$name]['fillable']              = true;
            $json[$name]['backend']['db']['type'] = preg_replace('/\(.*/', '', $column['Type']);
            $type                                 = preg_replace('/[a-z\W]/', '', $column['Type']);
            if ($json[$name]['backend']['db']['type'] != "enum" && $type != "") {
                $json[$name]['backend']['db']['size'] = preg_replace('/[a-z\W]/', '', $column['Type']);
            }
            $json[$name]['frontend']['label']        = 'labels.' . $column['Field'];
            $json[$name]['frontend']['placeholder']  = 'placeholders.' . $column['Field'];
            if(str_contains($name, ['password'])) {
                $json[$name]['frontend']['confirmation'] = true;

            }
            switch ($json[$name]['backend']['db']['type']) {
                case "char":
                case "varchar":
                case "tinytext":
                    $json[$name]['frontend']['type'] = !str_contains($name, ['password']) ? 'text' : 'password';
                    break;
                case "blob":
                case "text":
                    $json[$name]['frontend']['type'] = 'textarea';
                    break;
                case "enum":
                    $json[$name]['frontend']['type']      = 'select';
                    $json[$name]['backend']['db']['enum'] = explode(',', preg_replace('/enum|\(|\)|\'/', '', $column['Type']));
                    $json[$name]['frontend']['options']['type'] = 'enum';
                    foreach ($json[$name]['backend']['db']['enum'] as $value) {
                        $option = [
                            'value' => $value,
                            'label' => 'labels.' . strtolower($value),
                        ];
                        $json[$name]['frontend']['options']['values'][] = (object) $option;
                    }
                    break;
                case "set":
                case "time":
                    $json[$name]['frontend']['type'] = 'time';
                    break;
                case "year":
                    $json[$name]['frontend']['type'] = 'year';
                    break;
                case "tinyint":
                    $json[$name]['boolean'] = true;
                    $json[$name]['frontend']['type'] = 'checkbox';
                    $json[$name]['frontend']['tags'] = [
                        (object)[
                            "value" => 1,
                            "type" =>  "success",
                            "label" => "tags.active"
                        ],
                        (object)[
                            "value" => 0,
                            "type" => "danger",
                            "label" => "tags.inactive"
                        ]
                    ];
                    $json[$name]['frontend']['classes'] = ["col-md-2"];
                    break;
                case "smallint":
                case "mediumint":
                case "int":
                case "bigint":
                case "float":
                case "double":
                    $json[$name]['frontend']['type'] = 'number';
                    break;
                case "decimal":
                    $json[$name]['frontend']['type'] = 'money';
                    break;
                case "date":
                case "datetime":
                case "timestamp":
                    $json[$name]['frontend']['type'] = 'date';
                    break;
                default:
                    break;
            }
        }
        return $json;
    }

    protected function generateJson()
    {
        $exists = DB::select("SHOW TABLES LIKE '" . $this->entityPlural . "'");
        $json   = null;
        if (!empty($exists)) {
            $json                    = [];
            $json['tablename']       = $this->entityPlural;
            $json['views']           = $this->generateDefaultViews();
            $json['attributes']      = $this->generateJsonAttributes();
        }
        return $json;
    }

    protected function generateDefaultViews()
    {
        $views = [];

        $exceptFields = ["/id/", "/_id/", "/_at/", "/_by/"];
        $filteredColumns = Arr::where($this->columns, function($value) use ($exceptFields){
            return !preg_filter($exceptFields, array(),array($value['Field']));
        });

        foreach($filteredColumns as $column) {
            $views['create']['fields'][] = $column['Field'];
            $views['edit']['fields'][] = $column['Field'];
            $views['show']['fields'][] = $column['Field'];
            $views['index']['fields'][] = $column['Field'];
        }

        $views['index']['actions'] = $this->generateDefaultActions($this->entityPlural);

        return $views;
    }
    
    protected function generateDefaultActions()
    {
        return [
            (object) [
                "name"  => "show",
                "label" => "actions.show",
                "icon"  => "glyphicon glyphicon-eye-open",
                "route" => $this->entityPlural . ".show",
            ],
            (object) [
                "name"  => "edit",
                "label" => "actions.edit",
                "icon"  => "glyphicon glyphicon-edit",
                "route" => $this->entityPlural . ".edit",
            ],
            (object) [
                "name"  => "delete",
                "label" => "actions.delete",
                "icon"  => "glyphicon glyphicon-trash",
                "route" => $this->entityPlural . ".destroy",
            ],
        ];
    }
}
