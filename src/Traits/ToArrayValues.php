<?php

namespace ConnectMalves\JsonCrud\Traits;

trait ToArrayValues
{
    /**
     * Get a plain attribute (not a relationship).
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttributeValue($key)
    {
        $value = parent::getAttributeValue($key);

        // // If the attribute is listed as a enum, we will translate the value
        // if (in_array($key, $this->getTranslatable()) && !is_null($value)) {
        //     return trans($this->getEnumTranslationFile() . snake_case(strtolower($value)));
        // }

        if (!isset($value)) {
            return null;
        }

        return $value;
    }

    /**
     * Get the enum translation file.
     *
     * @return mixed
     */
    protected function getEnumTranslationFile()
    {
        return isset($this->enumTranslationFile) ? "{$this->enumTranslationFile}." : 'labels.';
    }

    /**
     * Convert the model's attributes to an array.
     *
     * @return array
     */
    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();
        $attributes = $this->addEnumAttributesToArray($attributes);
        $attributes = $this->addMoneyAttributesToArray($attributes);
        $attributes = $this->addDefaultValueToArray($attributes);
        
        return $attributes;
    }

    /**
     * Add the enum attributes to the attributes array.
     *
     * @param  array  $attributes
     * @return array
     */
    protected function addEnumAttributesToArray(array $attributes)
    {
        foreach ($this->getEnums() as $key) {
            if (! isset($attributes[$key])) {
                continue;
            }

            $attributes[$key] = trans($this->getEnumTranslationFile() . snake_case(strtolower($attributes[$key])));
        }

        return $attributes;
    }

    /**
     * Add translate money attributes.
     *
     * @param  array  $attributes
     * @return array
     */
    protected function addMoneyAttributesToArray(array $attributes)
    {
        foreach ($this->getMoneyAttributes() as $key => $value) {
            if (! isset($attributes[$key])) {
                continue;
            }

            $attributes[$key] = number_format($value, 2, ',', '.');
        }

        return $attributes;
    }

    /**
     * Add the default message to the attributes that is null.
     *
     * @param  array  $attributes
     * @return array
     */
    protected function addDefaultValueToArray(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            if (!isset($attributes[$key])) {
                $attributes[$key] = null;
            }
        }

        return $attributes;
    }
}
