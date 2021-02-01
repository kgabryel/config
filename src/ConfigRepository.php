<?php

namespace Frankie\Config;

use ArrayAccess;
use BadMethodCallException;
use Ds\Map;
use OutOfBoundsException;

class ConfigRepository implements ArrayAccess
{
    /** @var Map<Config> */
    private Map $configs;
    private ParserInterface $fileParser;
    private ParserInterface $folderParser;

    public function __construct(ParserInterface $fileParser, ParserInterface $folderParser)
    {
        $this->fileParser = $fileParser;
        $this->folderParser = $folderParser;
        $this->configs = new Map();
    }

    public function __clone()
    {
        $this->configs = clone $this->configs;
        $this->fileParser = clone $this->fileParser;
        $this->folderParser = clone $this->folderParser;
    }

    public function configExists(string $key): bool
    {
        return $this->configs->hasKey($key);
    }

    /**
     * @param string $key
     *
     * @return Config
     * @throws OutOfBoundsException
     */
    public function getConfig(string $key): Config
    {
        if (!$this->configs->hasKey($key)) {
            throw new OutOfBoundsException("Undefined key: $key");
        }
        return $this->configs[$key];
    }

    public function offsetExists($offset): bool
    {
        return $this->configs->hasKey($offset);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     * @throws OutOfBoundsException
     */
    public function offsetGet($offset)
    {
        if (!$this->configs->hasKey($offset)) {
            throw new OutOfBoundsException("Undefined key: $offset");
        }
        return $this->configs->get($offset);
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

    public function addFolder(string $path): self
    {
        $this->configs = $this->configs->merge(
            $this->folderParser->setPath($path)
                ->create()
                ->get()
        );
        return $this;
    }

    public function addFile(string $path): self
    {
        $this->configs = $this->configs->merge(
            $this->fileParser->setPath($path)
                ->create()
                ->get()
        );
        return $this;
    }
}
