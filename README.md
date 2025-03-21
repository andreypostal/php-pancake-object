# 🥞 Pancake Object

Light and simple helper to work with value objects by providing a serializer and hydrator using PHP Attributes.
It also provides a set of interfaces that allow you to customize the lib behavior to your needs.

## Installation

```
composer require andreypostal/php-pancake-object
```

## Usage

### Attributes usage

Currently, we have three main attributes available:
- ValueObject
- Item
- SkipItem

When creating your **Value Objects** you just need to add the attribute ``Item``
to each property that you want to hydrate/serialize.

```php
use \Andrey\PancakeObject\Attributes\Item;

// { "id": 123, "name": "my name" }
class MyObject {
    #[Item]
    public int $id;
    #[Item]
    public string $name;
}
```

In the case where you want to serialize/hydrate every property from an object

```php
use \Andrey\PancakeObject\Attributes\ValueObject;

// { "id": 123, "name": "my name" }
#[ValueObject]
class MyObject {
    public int $id;
    public string $name;
}
```

You can also combine both when need to add custom key, make an item required or even just skip some property.

```php
use \Andrey\PancakeObject\Attributes\ValueObject;
use \Andrey\PancakeObject\Attributes\Item;
use \Andrey\PancakeObject\Attributes\SkipItem;

// { "id": 123, "custom_name": "my name" }
#[ValueObject]
class MyObject {
    public int $id;
    #[Item(key: 'custom_name')]
    public string $name;
    #[SkipItem]
    public string $ignoredProperty;
}
```

In case the items are required to exist in the data array being used for hydration,
you can add the required flag in the attribute to include some basic required validation.

```php
use \Andrey\PancakeObject\Attributes\Item;

// { "id": 123 } or { "id": 123, "name": "my name" }
class MyObject {
    #[Item(required: true)]
    public int $id;
    #[Item]
    public string $name;
}
```

When some of the keys in your data are different from your object, you can include the custom key in the attribute.

```php
use \Andrey\PancakeObject\Attributes\Item;

// { "customer_name": "the customer name" }
class MyObject {
    #[Item(key: 'customer_name')]
    public string $name;
}
```

Also, if you have a property that is an array of other object, you can inform the class in the attribute using the ``type`` option.
This will work as a hint so the hydrator can instantiate the appropriate object. This works with enums as well.

```php
use \Andrey\PancakeObject\Item;
use \MyNamespace\MyOtherObj;

// { "list": [ { "key": "value" } ] }
class MyObject {
    /** @var MyOtherObj[] */
    #[Item(type: MyOtherObj::class)]
    public array $list;
}
```

The type option can be used to validate that all the items in an array have some desired type as well, like "string", "integer"...

### Hydrator and Serializer

In order to hydrate some value object following the attribute rules, you just need to use the SimpleHydrator class.
We also provide interfaces that allow you to customize the hydrator and key mapping strategy used (like snake case to pascal case).

The same applies to the SimpleSerializer class, used to serialize an object into an data array.

```php
use \Andrey\PancakeObject\SimpleHydrator;
use \Andrey\PancakeObject\SimpleSerializer;
use \MyNamespace\MyObject;

// Hydration phase
$hydrator = new SimpleHydrator();

$dataArray = $request->body->toArray();
$myObject = $hydrator->hydrate($dataArray, MyObject::class);

// Serialization phase
$serializer = new SimpleSerializer();
$arr = $serializer->serialize($myObject);

```
