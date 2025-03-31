<?php

namespace Utils;

use Andrey\PancakeObject\Attributes\Item;
use Andrey\PancakeObject\Attributes\SkipItem;
use Andrey\PancakeObject\Attributes\ValueObject;

enum ImEnum: string
{
    case A = 'B';
}

#[ValueObject]
readonly class ChildObject
{
    public ImEnum $enum;

    public function __construct(
        public string $iHaveAName,
        #[Item(key: 'different_one')]
        public string $butIHaveADifferentOne,
        /** @var int[] */
        #[Item(type: 'integer')]
        public array $andImAnArrayOfInt,
    ) {
    }
}

readonly class TestObject
{
    #[Item]
    public string $string;
    #[Item]
    public int $int;
    #[Item]
    public float $float;
    #[Item]
    public bool $bool;

    #[Item]
    public ImEnum $enum;

    #[Item(type: ImEnum::class)]
    public array $enumArr;

    #[Item]
    public string $itemName;

    #[Item(required: true)]
    public string $missingRequired;

    #[Item]
    public ChildObject $singleChild;

    #[Item]
    public ?int $nullableInt;

    #[Item(type: ChildObject::class)]
    public array $arrayOfChildren;

    #[SkipItem]
    public int $imSkippedJustIgnoreMe;

    public function __construct()
    {
        $this->string = 'string';
        $this->int = 1;
        $this->float = 1.2;
        $this->bool = true;
        $this->itemName = 'Item name';
        $this->missingRequired = 'im here';
        $this->nullableInt = null;
        $this->singleChild = new ChildObject('child', 'other n', [4, 5, 6]);
        $this->arrayOfChildren = [
            new ChildObject('n1', 'no1', []),
            new ChildObject('n2', 'no2', [1,2,3]),
        ];
        $this->enum = ImEnum::A;
        $this->enumArr = [ImEnum::A, ImEnum::A];
    }
}
