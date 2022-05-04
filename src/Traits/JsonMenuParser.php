<?php

namespace Wwwillian\JsonCrud\Traits;

use Webmozart\Assert\Assert;
use Webmozart\Json\JsonDecoder;
use Webmozart\Json\JsonValidator;
use App;

trait JsonMenuParser
{
    /**
     * The validated json menu.
     *
     * @var array
     */
    private $jsonMenu;

    /**
     * The json schema filename.
     *
     * @var string
     */
    private $jsonFile = "menu.json";
    
    
    /**
     * The constructor.
     *
     */
    public function __construct()
    {
        $this->deserializeJson();
    }

    /**
     * Get json menu instance.
     *
     * @return array
     */
    public function getJsonMenu()
    {
        return $this->jsonMenu;
    }

    /**
     * The json object deserialized.
     *
     * @var array
     */
    public function deserializeJson()
    {
        $jsonDecoder = new JsonDecoder();
        $jsonValidator = new JsonValidator();

        $jsonFile = base_path() . '/resources/json/' . $this->jsonFile;

        Assert::file($jsonFile, 'The file %s does not exist');
        Assert::endsWith($jsonFile, '.json', 'The file %s is not a json file');

        $this->jsonMenu = json_decode(file_get_contents($jsonFile), true);

        $this->validateJson($this->jsonMenu);

        foreach ($this->jsonMenu['controllers'] as $key => $menu) {
            $this->jsonMenu['controllers'][$key]['items'] = $this->orderItems(!isset($menu['order']) ? "": $menu['order'], $menu['items']);
        }
        
        App::instance('JsonParserObject', $this->jsonMenu);
    }

    /**
     * Get items ordered.
     *
     * @return array
     */
    protected function orderItems($order, $items)
    {
        if (isset($items['items'])) {
            $items['items'] = $this->orderItems(!isset($menu['order']) ? "" : $menu['order'], $items['items']);
        }

        if (isset($order) && gettype($order) == 'array') {
            $items = array_merge(array_flip($order), $items);
        } else {
            switch ($order) {
                case 'desc':
                    krsort($items);
                    break;

                case 'asc':
                    ksort($items);
                    break;
            }
        }

        return $items;
    }

    /**
     * Validate json controller.
     *
     */
    protected function validateJson($jsonMenu)
    {
        if (!isset($jsonMenu['controllers'])) {
            return;
        }

        foreach ($jsonMenu['controllers'] as $key => $menu) {
            Assert::keyExists($menu, 'items', $key . ' does not contain property ' . 'items');
            Assert::keyExists($menu, 'icon', $key . ' does not contain property ' . 'icon');
            $this->validateItems($menu['items'], $key);
        }
    }

    /**
     * Validate json items.
     *
     */
    protected function validateItems($items, $parent)
    {
        foreach ($items as $key => $subitems) {
            if (isset($subitems['items'])) {
                Assert::keyExists($subitems, 'icon', $parent . '->items->' . $key . '->items does not contain property ' . 'icon');                
                $this->validateItems($subitems['items'], $parent . '->items->' . $key);
            } else {
                Assert::keyExists($subitems, 'route', $parent . '->items->' . $key . ' does not contain property ' . 'route');
                Assert::keyExists($subitems['route'], 'type', $parent . '->items->' . $key . '->route' . ' does not contain property ' . 'type');
                Assert::regex($subitems['route']['type'], '/name|url/', $parent . '->items->' . $key . '->route->type does not match value ' . 'name or url');
                Assert::keyExists($subitems['route'], 'value', $parent . '->items->' . $key . '->route' . ' does not contain property ' . 'value');
                Assert::keyExists($subitems, 'icon', $parent . '->items->' . $key . ' does not contain property ' . 'icon');
            }
        }
    }
}
