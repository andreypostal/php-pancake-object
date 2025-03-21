<?php

namespace Andrey\PancakeObject\KeyMapping;

interface KeyMappingStrategy
{
    // -> Hydrate
    public function from(string $key): string;
    // <- Parse
    public function to(string $key): string;
}
