<?php

namespace Frankie\Config;

use Ds\Collection;
use Ds\Map;
use InvalidArgumentException;
use M1\Env\Parser;

class FileParser implements ParserInterface
{
    private string $file;
    /** @var Map<Config> */
    private Map $configs;
    private string $name;

    public function __construct()
    {
        $this->configs = new Map();
    }

    public function __clone()
    {
        $this->configs = clone $this->configs;
    }

    public function create(): ParserInterface
    {
        if ($this->file !== null) {
            $parser = new Parser(file_get_contents($this->file));
            $this->configs[$this->name] = new Config(new Map($parser->getContent()));
        }
        return $this;
    }

    /**
     * @return Map<Config>
     */
    public function get(): Collection
    {
        return $this->configs;
    }


    public function setPath(string $path): ParserInterface
    {
        if (!file_exists($path) || is_dir($path)) {
            throw new InvalidArgumentException("File '$path' doesn't exists or is directory.");
        }
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        if (strtolower($extension) !== 'env') {
            throw new InvalidArgumentException("File must have 'env' extension.");
        }
        $this->configs = new Map();
        $this->file = $path;
        $this->name = pathinfo($path, PATHINFO_FILENAME);
        return $this;
    }
}
