<?php
namespace Andrey\PancakeObject;

use Andrey\PancakeObject\Attributes\Item;
use Andrey\PancakeObject\Attributes\SkipItem;
use Andrey\PancakeObject\Attributes\ValueObject;
use Andrey\PancakeObject\KeyMapping\KeyMappingStrategy;
use Andrey\PancakeObject\KeyMapping\KeyMappingUnderscore;
use InvalidArgumentException;
use JsonException;
use LogicException;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

readonly class SimpleHydrator implements HydratorInterface
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
    public function hydrate(array $data, string $class): object
    {
        $reflectionClass = new ReflectionClass($class);
        return $this->processClass($reflectionClass, $data);
    }

    /**
     * @throws ReflectionException
     */
    private function processClass(ReflectionClass $class, array $data): object
    {
        $instance = $class->newInstanceWithoutConstructor();
        $skipAttributeCheck = ($class->getAttributes(ValueObject::class)[0] ?? null) !== null;

        $properties = $class->getProperties();
        foreach ($properties as $property) {
            $payload = $this->processProperty($property, $data, $skipAttributeCheck);

            if ($payload->skipped) {
                continue;
            }

            if ($payload->error !== null) {
                throw $payload->error;
            }

            $property->setValue($instance, $payload->data);
        }

        return $instance;
    }

    /**
     * @throws ReflectionException
     */
    private function processProperty(ReflectionProperty $property, array $data, bool $skipAttributeCheck): Payload
    {
        // Check if property has ItemAttribute
        $attributes = $property->getAttributes(Item::class);
        $attr = $attributes[0] ?? null;

        // Skip attribute has precedence
        $skipAttribute = $property->getAttributes(SkipItem::class);
        $hasSkipAttribute = ($skipAttribute[0] ?? null) !== null;

        // We do not touch this property in this case
        if ($hasSkipAttribute || ($attr === null && !$skipAttributeCheck)) {
            return new Payload(skipped: true);
        }

        /** @var Item $item we set to an empty new item, means that it has ValueObject attribute */
        $item = $attr?->newInstance() ?? new Item();

        // If the item specific set a key to the property, then we use it, otherwise convert it using the strategy
        $key = $item->key ?? $this->keyStrategy->from($property->getName());

        // Simple validation for required items
        $arrKeyExists = array_key_exists($key, $data);
        if ($item->required && !$arrKeyExists) {
            return new Payload(error: new InvalidArgumentException(sprintf('required item <%s> not found', $key)));
        }

        if (!$arrKeyExists) {
            return $this->buildNullableResponse($property, null);
        }

        if ($property->getType()?->isBuiltin()) {
            return $this->handleBuiltin($data, $key, $property, $item);
        }

        // Enum or a child/internal value object
        return $this->handleCustomType($data[$key], $property->getType()?->getName());
    }

    /**
     * @throws ReflectionException
     */
    private function handleBuiltin(array $data, string $key, ReflectionProperty $property, Item $item): Payload
    {
        // If we define a type in the attribute and the property is an array, we perform simple type validation
        if ($item->type !== null && $property->getType()?->getName() === 'array') {
            $output = [];
            $classExists = class_exists($item->type);
            foreach ($data[$key] ?? [] as $k => $v) {
                $value = $v;
                if ($classExists && gettype($v) === 'array') {
                    $payload = $this->handleCustomType($value, $item->type);

                    if ($payload->skipped) {
                        continue;
                    }

                    if ($payload->error !== null) {
                        return $payload;
                    }

                    $value = $payload->data;
                } else if ($classExists || gettype($v) !== $item->type) {
                    return new Payload(
                        error: new LogicException(sprintf('expected array with items of type <%s> but found <%s>', $item->type, gettype($v))),
                    );
                }

                $output[$k] = $value;
            }
            return new Payload(data: $output);
        }

        // If no data is set, returns null which will be ignored by the set value, falling back to undefined or default value
        return $this->buildNullableResponse($property, $data[$key] ?? null);
    }

    /**
     * @throws ReflectionException
     */
    private function handleCustomType(mixed $value, string $type): mixed
    {
        $typeReflection = new ReflectionClass($type);
        if ($typeReflection->isEnum()) {
            return new Payload(data: call_user_func($type.'::tryFrom', $value));
        }

        // Recursively hydrate child/internal value objects
        return new Payload(data: $this->hydrate(
            data: $value,
            class: $type,
        ));
    }

    private function buildNullableResponse(ReflectionProperty $property, mixed $value): Payload
    {
        if ($value !== null || $property->getType()->allowsNull()) {
            return new Payload(data: $value);
        }
        return new Payload(skipped: true);
    }
}
