<?php

use Andrey\PancakeObject\Attributes\ValueObject;
use Andrey\PancakeObject\Attributes\Item;
use Andrey\PancakeObject\SimpleHydrator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\TestCase;
use Utils\ChildObject;
use Utils\TestObject;

#[CoversClass(Item::class)]
#[CoversClass(ValueObject::class)]
#[CoversClass(SimpleHydrator::class)]
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
            'missing_required' => 'Im here',
            'item_name' => 'Different name',
            'single_child' => [
                'i_have_a_name' => 'this is the name',
                'different_one' => 'other one',
                'and_im_an_array_of_int' => [ 0, 1, 2],
            ],
            'array_of_children' => [
                [
                    'i_have_a_name' => 'c1',
                    'different_one' => 'o1',
                    'and_im_an_array_of_int' => [ 3, 4, 5],
                ],
                [
                    'i_have_a_name' => 'c2',
                    'different_one' => 'o2',
                    'and_im_an_array_of_int' => [ 6, 7, 8],
                ]
            ]
        ];

        $hydrator = new SimpleHydrator();

        /** @var TestObject $obj */
        $obj = $hydrator->hydrate($data, TestObject::class);

        $this->assertEquals('str', $obj->string);
        $this->assertEquals('Different name', $obj->itemName);
        $this->assertEquals(10, $obj->int);
        $this->assertEquals(3.14, $obj->float);
        $this->assertFalse($obj->bool);
        $this->assertNull($obj->nullableInt);

        $this->assertInstanceOf(ChildObject::class, $obj->singleChild);
        $this->assertIsArray($obj->singleChild->andImAnArrayOfInt);
        $this->assertSame($data['single_child']['and_im_an_array_of_int'], $obj->singleChild->andImAnArrayOfInt);

        $this->assertIsArray($obj->arrayOfChildren);
        foreach ($obj->arrayOfChildren as $ci => $child) {
            $childArr = $data['array_of_children'][$ci];

            $this->assertInstanceOf(ChildObject::class, $child);
            $this->assertEquals($childArr['i_have_a_name'], $child->iHaveAName);
            $this->assertEquals($childArr['different_one'], $child->butIHaveADifferentOne);

            $this->assertIsArray($child->andImAnArrayOfInt);
            $this->assertSame($childArr['and_im_an_array_of_int'], $child->andImAnArrayOfInt);

            foreach ($child->andImAnArrayOfInt as $i => $int) {
                $this->assertIsInt($int);
                $this->assertEquals($childArr['and_im_an_array_of_int'][$i], $int);
            }
        }
    }

    /**
     * @throws ReflectionException
     */
    public function testMissingRequiredItem(): void
    {
        $data = [];

        $hydrator = new SimpleHydrator();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('required item <missing_required> not found');
        $hydrator->hydrate($data, TestObject::class);
    }
}
