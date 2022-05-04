<?php

namespace Wwwillian\JsonCrud\Traits;

use App;
use Webmozart\Assert\Assert;
use Webmozart\Json\JsonDecoder;
use Webmozart\Json\JsonValidator;
use Illuminate\Support\Facades\DB;

trait JsonParser
{
    abstract public function getJsonFilename();
    abstract public function getModule();

    /**
     * The validated json object.
     *
     * @var array
     */
    protected $jsonObject;

    /**
     * The json schema filename.
     *
     * @var string
     */
    private $jsonSchema = "schema.json";

    /**
     * The json object deserialized.
     *
     * @var array
     */
    public function deserializeJson()
    {
        $jsonDecoder   = app()->make(JsonDecoder::class);
        $jsonValidator = app()->make(JsonValidator::class);

        $module     = $this->getModule();
        $jsonFile   = $this->getJsonFilename();
        $jsonSchema = __DIR__ . '/../resources/json/' . $this->jsonSchema;

        $path = base_path();
        if (!is_null($module)) {
            $path .= '/modules/' . $module;
        }
        $path .= '/resources/json/';

        Assert::file($path . $jsonFile, 'The file %s does not exist');
        Assert::endsWith($path . $jsonFile, '.json', 'The file %s is not a json file');

        $json   = $jsonDecoder->decodeFile($path . $jsonFile);
        $errors = $jsonValidator->validate($json, $jsonSchema);

        if (count($errors) > 0) {
            throw new \RuntimeException("" . implode(', ', $errors));
        }

        $this->jsonObject = json_decode(file_get_contents($path . $jsonFile), true);
    }

    public function frontend()
    {
        $json = $this->jsonObject;
        $json = array_only($json, ['attributes']);
        foreach ($json['attributes'] as $key => $attribute) {
            $json['attributes'][$key] = array_except($attribute, ['backend']);

            if(data_get($attribute, 'frontend.options.type') == "query") {
                $options = $this->getQueryOptions(data_get($attribute, 'frontend.options'));
                data_set($json['attributes'][$key], 'frontend.options.values', $options);
            } 
        }

        return $json['attributes'];
    }

    public function views()
    {
        return array_only($this->jsonObject, ['views'])['views'];
    }

    public function getJsonInstance()
    {
        return App::make('JsonParserObject');
    }

    public function getJson()
    {
        return $this->jsonObject;
    }

    public function getJsonTimestamps()
    {
        return $this->jsonObject['timestamps'] ?? null;
    }

    public function getJsonAttributes()
    {
        return $this->jsonObject['attributes'] ?? [];
    }

    public function getJsonTablename()
    {
        return $this->jsonObject['tablename'] ?? null;
    }

    public function getJsonAttributeDefaultValue($attribute)
    {
        return $this->jsonObject['attributes'][$attribute]['backend']['default'] ?? null;
    }

    public function getJsonAttributeIsFillable($attribute)
    {
        return $this->jsonObject['attributes'][$attribute]['fillable'] ?? true;
    }

    public function getJsonAttributeIsVisible($attribute)
    {
        return $this->jsonObject['attributes'][$attribute]['visible'] ?? true;
    }

    public function getJsonAttributeIsRequired($attribute)
    {
        return $this->jsonObject['attributes'][$attribute]['required'] ?? false;
    }

    public function getJsonAttributeIsUnique($attribute)
    {
        return $this->jsonObject['attributes'][$attribute]['unique'] ?? false;
    }

    public function getJsonAttributeIsBoolean($attribute)
    {
        return $this->jsonObject['attributes'][$attribute]['boolean'] ?? false;
    }

    public function getJsonAttributeIsMoney($attribute)
    {
        $type = $this->jsonObject['attributes'][$attribute]['frontend']['type'] ?? null;
        return $type == 'money';
    }

    public function getJsonAttributeIsTranslatable($attribute)
    {
        return $this->jsonObject['attributes'][$attribute]['frontend']['options']['translate'] ?? false;
    }

    public function getJsonAttributeValidationRules($attribute)
    {
        return $this->jsonObject['attributes'][$attribute]['backend']['validations'] ?? [];
    }

    public function getJsonAttributeDb($attribute)
    {
        return $this->jsonObject['attributes'][$attribute]['backend']['db'];
    }

    public function getJsonAttributeSanitizers($attribute)
    {
        return $this->jsonObject['attributes'][$attribute]['sanitizers'] ?? null;
    }

    public function getJsonAttributeHasTag($attribute)
    {
        return isset($this->jsonObject['attributes'][$attribute]['frontend']['tag'])  && !empty($this->jsonObject['attributes'][$attribute]['frontend']['tag']);
    }

    public function getQueryOptions($options)
    {
        $name = $options['columns']['name'];
        $value = $options['columns']['value'];
        $where = $options['columns']['where'] ?? null;
        $query = DB::table($options['table'])
            ->select("{$name} as label", "{$value} as value");

        if(isset($where)) {
            $query->whereRaw($where);
        }
        
        $resultset = $query->get()
            ->map(function ($item) use($options) {
                if(isset($options['translate']) && $options['translate'] == true) {
                    $item->label = strtolower('labels.' . $item->label);
                }

                return (array)$item;
            })
            ->toArray();

        return $resultset;
    }
}
