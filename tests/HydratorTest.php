<?php

use Andrey\PancakeObject\Attributes\ValueObject;
use Andrey\PancakeObject\Attributes\Item;
use Andrey\PancakeObject\SimpleHydrator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\TestCase;
use Utils\SimpleTestObject;

#[CoversClass(Item::class)]
#[CoversClass(ValueObject::class)]
final class HydratorTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testSimpleHydrate(): void
    {
        $data = [
            'string' => 'str',
            'int' => 10,
            'float' => 3.14,
            'bool' => false,
        ];

        $hydrator = new SimpleHydrator();

        $obj = $hydrator->hydrate($data, SimpleTestObject::class);

        $this->assertEquals('str', $obj->string);
        $this->assertEquals(10, $obj->int);
        $this->assertEquals(3.14, $obj->float);
        $this->assertFalse($obj->bool);
    }
}
