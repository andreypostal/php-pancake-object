<?php
namespace Andrey\PancakeObject;

use Andrey\PancakeObject\Attributes\Item;
use Andrey\PancakeObject\Attributes\SkipItem;
use Andrey\PancakeObject\Attributes\ValueObject;
use Andrey\PancakeObject\KeyMapping\KeyMappingStrategy;
use Andrey\PancakeObject\KeyMapping\KeyMappingUnderscore;
use LogicException;
use ReflectionClass;
use ReflectionException;

readonly class SimpleSerializer implements SerializerInterface
{
    private KeyMappingStrategy $keyStrategy;

    public function __construct(
        ?KeyMappingStrategy $keyStrategy = null,
    ) {
        $this->keyStrategy = $keyStrategy ?: new KeyMappingUnderscore();
    }

    /**
     * @throws ReflectionException
     */
    public function serialize(object $obj): array
    {
        $class = new ReflectionClass($obj);
        $canSkipAttributeCheck = ($class->getAttributes(ValueObject::class)[0] ?? null) !== null;
        $output = [];

        $properties = $class->getProperties();
        foreach ($properties as $property) {
            // Check if property has ItemAttribute
            $attributes = $property->getAttributes(Item::class);
            $attr = $attributes[0] ?? null;

            // Skip attribute has precedence
            $skipAttribute = $property->getAttributes(SkipItem::class);
            $hasSkipAttribute = ($skipAttribute[0] ?? null) !== null;

            if ($hasSkipAttribute || ($attr === null && !$canSkipAttributeCheck)) {
                continue;
            }

            /** @var Item $item we set to an empty new item, means that it has ValueObject attribute */
            $item = $attr?->newInstance() ?? new Item();

            // If the item specific set a key to the property, then we use it, otherwise convert it using the strategy
            $key = $item->key ?? $this->keyStrategy->to($property->name);

            if ($property->getType()?->isBuiltin()) {
                $output[$key] = $this->handlePossibleArray($item, $property->getValue($obj));
                continue;
            }

            $class = new ReflectionClass($property->getValue($obj));
            if ($class->isEnum()) {
                $output[$key] = $property->getValue($obj)->value;
                continue;
            }

            // Serialize child/internal value object
            $output[$key] = $this->serialize($property->getValue($obj));
        }

        return $output;
    }

    /**
     * @param Item $item
     * @param mixed $value
     * @return mixed
     * @throws ReflectionException
     *
     */
    private function handlePossibleArray(Item $item, mixed $value): mixed
    {
        if (gettype($value) !== 'array') {
            return $value;
        }

        if (!class_exists($item->type)) {
            throw new LogicException(sprintf('expected array with items of type <%s> but found <%s>', $item->type, gettype($value)));
        }

        $class = new ReflectionClass($item->type);
        $isEnum = $class->isEnum();

        return array_reduce(
            array: $value,
            callback: function(array $l, mixed $c) use ($isEnum): array {
                if ($isEnum) {
                    $v = $c->value;
                } else {
                    $v = $this->serialize($c);
                }
                $l[] = $v;
                return $l;
            },
            initial: [],
        );
    }
}
