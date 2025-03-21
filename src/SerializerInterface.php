<?php

namespace Andrey\PancakeObject;

interface SerializerInterface
{
    public function serialize(object $obj): array;
}
