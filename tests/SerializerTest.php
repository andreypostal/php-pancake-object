<?php

use Andrey\PancakeObject\Attributes\Item;
use Andrey\PancakeObject\Attributes\ValueObject;
use Andrey\PancakeObject\SimpleSerializer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Utils\SimpleTestObject;

#[CoversClass(Item::class)]
#[CoversClass(ValueObject::class)]
final class SerializerTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testSimpleSerialize(): void
    {
        $this->assertSimpleSerializedObject(new SimpleTestObject());
    }

    /**
     * @throws ReflectionException
     */
    private function assertSimpleSerializedObject(object $obj): void
    {
        $serializer = new SimpleSerializer();
        $arr = $serializer->serialize($obj);

        $this->assertArrayHasKey('string', $arr);
        $this->assertArrayHasKey('int', $arr);
        $this->assertArrayHasKey('float', $arr);
        $this->assertArrayHasKey('bool', $arr);
        $this->assertArrayHasKey('item_name', $arr);

        $this->assertIsBool($arr['bool']);
        $this->assertTrue($arr['bool']);

        $this->assertIsInt($arr['int']);
        $this->assertEquals(1, $arr['int']);

        $this->assertIsFloat($arr['float']);
        $this->assertEquals(1.2, $arr['float']);

        $this->assertIsString($arr['string']);
        $this->assertEquals('string', $arr['string']);

        $this->assertIsString($arr['item_name']);
        $this->assertEquals('Item name', $arr['item_name']);
    }
}
