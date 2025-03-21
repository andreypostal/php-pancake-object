<?php

namespace Utils;

use Andrey\PancakeObject\Attributes\Item;

readonly class SimpleTestObject
{
    #[Item(required: true)]
    public string $string;
    #[Item]
    public int $int;
    #[Item]
    public float $float;
    #[Item]
    public bool $bool;

    public function __construct()
    {
        $this->string = 'string';
        $this->int = 1;
        $this->float = 1.2;
        $this->bool = true;
    }
}
