<?php

namespace Frankie\Config;

use ArrayAccess;
use BadMethodCallException;
use Ds\Map;
use OutOfBoundsException;

class Config implements ArrayAccess
{
    private Map $values;

    public function __construct(Map $values)
    {
        $this->values = $values;
    }

    public function __clone()
    {
        $this->values = clone $this->values;
    }

    public function has(string $key): bool
    {
        return $this->values->hasKey($key);
    }

    /**
     * @param string $key
     *
     * @return mixed
     * @throws OutOfBoundsException
     */
    public function get(string $key)
    {
        if (!$this->values->hasKey($key)) {
            throw new OutOfBoundsException("Undefined key: $key");
        }
        return $this->values->get($key);
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->values->hasKey($offset);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     * @throws OutOfBoundsException
     */
    public function offsetGet($offset)
    {
        if (!$this->values->hasKey($offset)) {
            throw new OutOfBoundsException("Undefined key: $offset");
        }
        return $this->values->get($offset);
    }

    public function offsetSet($offset, $value): void
    {
        throw new BadMethodCallException(
            'Array access of class ' . \get_class($this) . ' is read-only!'
        );
    }

    public function offsetUnset($offset): void
    {
        throw new BadMethodCallException(
            'Array access of class ' . \get_class($this) . ' is read-only!'
        );
    }
}
