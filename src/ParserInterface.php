<?php

namespace Frankie\Config;

use Ds\Collection;

interface ParserInterface
{
    public function __construct();

    public function setPath(string $path): self;

    public function create(): self;

    public function get(): Collection;
}
