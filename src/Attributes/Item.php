<?php
namespace Andrey\PancakeObject\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Item
{
    public function __construct(
        public ?string $key = null,
        public bool $required = false,
        public ?string $type = null,
        public mixed $default = null,
    ) { }
}
