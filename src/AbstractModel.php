<?php

namespace Dsh\AbstractModel;

use ReflectionProperty;
use ReflectionClass;
use ReflectionException;
use Psr\Container\ContainerInterface as Container;
use Dsh\Exception\AbstractModelException;

/**
 * The AbstractModel makes liberal use of the Reflection classes.
 *
 * Class AbstractModel
 * @package Freebeer\Model
 */
abstract class AbstractModel implements Container
{
    /**
     * Used in the buildStore() method for the default property visibility to
     * access within a method. By default it is only Public and Protected
     * properties that are accessible from the AbstractModel
     *
     * @var int
     */
    const USE_DEFAULT = ReflectionProperty::IS_PUBLIC;

    /**
     * @var int
     */
    const USE_PUBLIC = ReflectionProperty::IS_PUBLIC;

    /**
     * @var int
     */
    const USE_PROTECTED = ReflectionProperty::IS_PRIVATE;

    /**
     * @var int
     */
    const USE_PRIVATE = ReflectionProperty::IS_PRIVATE;

    /**
     * @var int
     */
    const USE_STATIC = ReflectionProperty::IS_STATIC;

    /**
     * @var int
     */
    const USE_ALL = (
        ReflectionProperty::IS_PUBLIC    |
        ReflectionProperty::IS_PROTECTED |
        ReflectionProperty::IS_PRIVATE   |
        ReflectionProperty::IS_STATIC
    );

    /**
     * @var array
     */
    protected $store = [];

    /**
     * @param int $mask
     * @return $this
     */
    public function init(int $mask = 0)
    {
        $this->buildStore($mask);
        return $this;
    }

    /**
     * The only supported magic methods at the moment are getters and setters.
     *
     * @param $name
     * @param $arguments
     * @return AbstractModel|mixed|null
     */
    public function __call($name, $arguments)
    {
        $method = substr($name, 0, 3);

        if ($method === 'set') {
            $prop = substr($name, 3, (strlen($name) - 3));
            $prop = lcfirst($prop);

            return $this->set($prop, $arguments[0]);
        }

        if ($method === 'get') {
            $prop = substr($name, 3, (strlen($name) - 3));
            $prop = lcfirst($prop);

            return $this->get($prop);
        }
    }

    /**
     * @param string $key
     * @return mixed
     * @throws AbstractModelException
     */
    public function get($key)
    {
        if (!$this->has($key)) {
            throw new AbstractModelException("Property '{$key}' does not exist.");
        }

        return $this->store[$key];
    }

    /**
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->store);
    }


    /**
     * @param $key
     * @param $value
     * @throws AbstractModelException
     */
    public function set($key, $value)
    {
        if (!$this->has($key)) {
            throw new AbstractModelException("Property '{$key}' does not exist.");
        }

        $this->store[$key] = $value;
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return array_keys($this->store);
    }

    /**
     * @param int $mask     Defaults to only Public and Protected properties
     * @return void
     */
    protected function buildStore(int $mask = 0)
    {
        if ($mask === 0) {
            $mask = self::USE_DEFAULT;
        }

        $className = static::class;
        $reflection = new ReflectionClass($className);

        foreach ($reflection->getProperties($mask) as $property) {
            $name = $property->getName();

            if ($name === 'store') {
                continue;
            }

            // brute force access to protected parameters, fail safe
            $property->setAccessible(true);

            $this->store[$name] = $property->getValue(new $className);
        }
    }
}