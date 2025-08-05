<?php

use Andrey\PancakeObject\Attributes\Item;
use Andrey\PancakeObject\Attributes\ValueObject;
use Andrey\PancakeObject\SimpleSerializer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Utils\ImEnum;
use Utils\TestObject;

#[CoversClass(Item::class)]
#[CoversClass(ValueObject::class)]
#[CoversClass(SimpleSerializer::class)]
final class SerializerTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testSimpleSerialize(): void
    {
        $serializer = new SimpleSerializer();
        $data = $serializer->serialize(new TestObject());

        $this->assertIsString($data["string"]);
        $this->assertSame("string", $data["string"]);

        $this->assertIsInt($data["int"]);
        $this->assertSame(1, $data["int"]);

        $this->assertIsFloat($data["float"]);
        $this->assertSame(1.2, $data["float"]);

        $this->assertIsBool($data["bool"]);
        $this->assertTrue($data["bool"]);

        $this->assertIsString($data["item_name"]);
        $this->assertSame("Item name", $data["item_name"]);

        $this->assertIsString($data["missing_required"]);
        $this->assertSame("im here", $data["missing_required"]);

        $this->assertIsArray($data["single_child"]);
        $this->assertArrayHasKey("i_have_a_name", $data["single_child"]);
        $this->assertSame("child", $data["single_child"]["i_have_a_name"]);

        $this->assertArrayHasKey("different_one", $data["single_child"]);
        $this->assertSame("other n", $data["single_child"]["different_one"]);

        $this->assertArrayHasKey("and_im_an_array_of_int", $data["single_child"]);
        $this->assertIsArray($data["single_child"]["and_im_an_array_of_int"]);
        $this->assertSame([4, 5, 6], $data["single_child"]["and_im_an_array_of_int"]);

        $this->assertIsArray($data["array_of_children"]);
        $this->assertCount(2, $data["array_of_children"]);

        $this->assertSame("n1", $data["array_of_children"][0]["i_have_a_name"]);
        $this->assertSame("no1", $data["array_of_children"][0]["different_one"]);
        $this->assertIsArray($data["array_of_children"][0]["and_im_an_array_of_int"]);
        $this->assertEmpty($data["array_of_children"][0]["and_im_an_array_of_int"]);

        $this->assertSame("n2", $data["array_of_children"][1]["i_have_a_name"]);
        $this->assertSame("no2", $data["array_of_children"][1]["different_one"]);
        $this->assertIsArray($data["array_of_children"][1]["and_im_an_array_of_int"]);
        $this->assertSame([1, 2, 3], $data["array_of_children"][1]["and_im_an_array_of_int"]);

        $this->assertEquals(ImEnum::A->value, $data['enum']);
        $this->assertSame([ImEnum::A->value, ImEnum::A->value], $data['enum_arr']);
    }
}
