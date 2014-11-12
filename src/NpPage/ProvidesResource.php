<?php
/*
 *
 *
 * @copyright Copyright (c) 2014-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace NpPage;

use Traversable;
use Zend\Stdlib\ArrayUtils;
use Zend\View\Variables;

trait ProvidesResource
{
    public $name = '';

    public $class;

    static public $nameDelimiter = '::';

    protected $options = array();

    /**
     *
     * @var Variables
     */
    protected $properties;

    protected $prototype;

    public function configureResource(array $config)
    {
        foreach ($config as $key => $value)
        {
            switch ($key) {
                case "name":
                    $this->setName($value);
                    break;
                case "class":
                    $this->setResourceClass($value);
                    break;
                case "options":
                    $this->setOptions($value);
                    break;
                case "properties":
                    $this->setProperties($value);
                    break;
                case "default_options":
                    $this->setDefaultOptions($value);
                    break;
                case "prototype":
                    if ($value instanceof ResourceInterface) {
                        $this->setPrototype($value);
                    }
                    break;
                default:
                    $this->setProperty($key, $value);
                    break;
            }
        }
        return $this;
    }

    public function getResourceId()
    {
        if (strlen($this->name)) {
            return $this->getResourceClass() . self::$nameDelimiter . $this->name;
        }
        else {
            return $this->getResourceClass();
        }

    }

    public function setName($name)
    {
        $this->name = (string) $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setResourceClass($class)
    {
        $this->class = (string) $class;
    }

    public function getResourceClass()
    {
        if (!isset($this->class)) {
            $this->class = get_class($this);
        }

        return $this->class;
    }

    public function setPrototype(ResourceInterface $prototype)
    {
        $this->prototype = $prototype;
        $this->onInjectPrototype();
    }

    protected function onInjectPrototype(){}

    /**
     * Set a single option
     *
     * @param  string $name
     * @param  mixed $value
     * @return ViewModel
     */
    public function setOption($name, $value)
    {
        $this->options[(string) $name] = $value;
        return $this;
    }

    /**
     * Get a single option
     *
     * @param  string       $name           The option to get.
     * @param  mixed|null   $default        (optional) A default value if the option is not yet set.
     * @return mixed
     */
    public function getOption($name, $default = null)
    {
        $name = (string)$name;
        //defaultOptionsは既にマージされています
        if (! array_key_exists($name, $this->options)) {
            if (isset($this->prototype)) {
                return $this->prototype->getOption($name, $default);
            } else {
                return $default;
            }
        }
        return $this->options[$name];
    }

    /**
     * Set renderer options/hints en masse
     *
     * @param array|\Traversable $options
     * @throws \Zend\View\Exception\InvalidArgumentException
     * @return self
     */
    public function setOptions($options, $clear = false)
    {
        // Assumption is that lowest common denominator for renderer configuration
        // is an array
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (!is_array($options)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s: expects an array, or Traversable argument; received "%s"',
                __METHOD__,
                (is_object($options) ? get_class($options) : gettype($options))
            ));
        }

        if ($clear) {
            $this->options = $options;
        }
        else {
            $this->options = ArrayUtils::merge($this->options, $options);
        }

        return $this;
    }

    /**
     * Set renderer options/hints en masse
     *
     * @param array|\Traversable $defaultOptions
     * @throws \Zend\View\Exception\InvalidArgumentException
     * @return self
     */
    public function setDefaultOptions($defaultOptions)
    {
        // Assumption is that lowest common denominator for renderer configuration
        // is an array
        if ($defaultOptions instanceof Traversable) {
            $defaultOptions = ArrayUtils::iteratorToArray($defaultOptions);
        }

        if (!is_array($defaultOptions)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s: expects an array, or Traversable argument; received "%s"',
                __METHOD__,
                (is_object($defaultOptions) ? get_class($defaultOptions) : gettype($defaultOptions))
            ));
        }

        $this->options = ArrayUtils::merge($defaultOptions, $this->options);
        return $this;
    }

    /**
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     *
     * @param Variables|ArrayObject|array|Traversable $properties
     * @param boolean $clear
     * @throws Exception\InvalidArgumentException
     */
    public function setProperties($properties, $clear = false)
    {
        if ($clear && ($properties instanceof Variables)) {
            $this->properties = $properties;
        }
        // Assumption is that lowest common denominator for renderer configuration
        // is an array
        //Variablesであっても、配列に変換される。
        if ($properties instanceof Traversable) {
            $properties = ArrayUtils::iteratorToArray($properties);
        }

        if (!is_array($properties)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s: expects an array, or Traversable argument; received "%s"',
                __METHOD__,
                (is_object($properties) ? get_class($properties) : gettype($properties))
            ));
        }

        if ($clear) {
            $this->getProperties()->exchangeArray($properties);
        } else {
            $this->getProperties()->assign($properties);
        }

        return $this;
    }

    public function getProperties()
    {
        if (!isset($this->properties)) {
            $this->properties = new Variables;
        }

        return $this->properties;
    }

    public function issetProperty($name, $recursive = true)
    {
        if (isset($this->getProperties()[$name])) {
            return true;
        }

        if (! $recursive) {
            return false;
        }

        if (isset($this->prototype) && $this->prototype->issetProperty($name, true)) {
            return true;
        }

        return false;
    }

    public function getProperty($name, $default = null)
    {
        if (isset($this->getProperties()[$name])) {
            return $this->getProperties()[$name];
        }

        if (isset($this->prototype)) {
            return $this->prototype->getProperty($name, $default);
        }

        return $default;
    }

    public function setProperty($name, $value)
    {
        $this->getProperties()[$name] = $value;
    }
}