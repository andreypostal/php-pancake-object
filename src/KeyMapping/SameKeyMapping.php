<?php /** @noinspection ForeachInvariantsInspection */

namespace Andrey\PancakeObject\KeyMapping;

class SameKeyMapping implements KeyMappingStrategy
{
    public function from(string $key): string
    {
        return $key;
    }

    public function to(string $key): string
    {
        return $key;
    }
}
