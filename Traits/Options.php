<?php

namespace Apli\Support\Traits;

/**
 * Trait Options.
 */
trait Options
{
    /**
     * Property options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Method to get property Options.
     *
     * @param $name
     * @param null $default
     *
     * @return null
     */
    public function getOption($name, $default = null)
    {
        if (array_key_exists($name, $this->options)) {
            return $this->options[$name];
        }

        return $default;
    }

    /**
     * Method to set property options.
     *
     * @param $name
     * @param $value
     *
     * @return $this
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;

        return $this;
    }

    /**
     * Method to get property Options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Method to set property options.
     *
     * @param $options
     *
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }
}
