<?php

namespace Frankie\Config;

use Ds\Collection;
use Ds\Map;
use Ds\Queue;
use InvalidArgumentException;
use M1\Env\Parser;

class FolderParser implements ParserInterface
{
    private string $path;
    private Queue $fileList;
    /** @var Map<Config> */
    private Map $configs;

    public function __construct()
    {
        $this->fileList = new Queue();
        $this->configs = new Map();
    }

    public function __clone()
    {
        $this->configs = clone $this->configs;
        $this->fileList = clone $this->fileList;
    }

    protected function createList(): void
    {
        $files = scandir($this->path);
        for ($i = 2, $iMax = \count($files); $i < $iMax; $i++) {
            $fullPath = $this->path . DIRECTORY_SEPARATOR . $files[$i];
            $ext = pathinfo($fullPath, PATHINFO_EXTENSION);
            if (strtolower($ext) === 'env') {
                $this->fileList->push($fullPath);
            }
        }
    }

    /**
     * @return ParserInterface
     */
    public function create(): ParserInterface
    {
        if ($this->path !== null) {
            foreach ($this->fileList as $file) {
                $name = pathinfo($file, PATHINFO_FILENAME);
                $this->configs[$name] = new Map();
                $parser = new Parser(file_get_contents($file));
                $this->configs[$name] = new Config(new Map($parser->getContent()));
            }
        }
        return $this;
    }

    /**
     * @return Map
     */
    public function get(): Collection
    {
        return $this->configs;
    }

    public function setPath(string $path): ParserInterface
    {
        if (!file_exists($path) || !is_dir($path)) {
            throw new InvalidArgumentException(
                "Directory '$path' doesn't exists or isn't directory."
            );
        }
        $this->path = rtrim(
            str_replace(
                [
                    '\\',
                    '/'
                ],
                DIRECTORY_SEPARATOR,
                $path
            ),
            DIRECTORY_SEPARATOR
        );
        $this->configs = new Map();
        $this->fileList = new Queue();
        $this->createList();
        return $this;
    }
}
