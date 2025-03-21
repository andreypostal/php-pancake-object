<?php

namespace Andrey\PancakeObject;

interface HydratorInterface
{
    public function hydrate(array $data, string $class): object;
}
