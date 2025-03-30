<?php
namespace KeyMapping;

use Andrey\PancakeObject\KeyMapping\KeyMappingUnderscore;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(KeyMappingUnderscore::class)]
final class KeyMappingUnderscoreTest extends TestCase
{
    public function testFromKey(): void
    {
        $strategy = new KeyMappingUnderscore();
        $result = $strategy->from('fromMyKey');
        $this->assertEquals('from_my_key', $result);
    }

    public function testFromKeyStartingWithUnderscore(): void
    {
        $strategy = new KeyMappingUnderscore();
        $result = $strategy->from('_fromMyKey');
        $this->assertEquals('__from_my_key', $result);
    }

    /**
     * For pascal case maintain behavior otherwise we cannot keep
     * the equality (parser -> serializer and serializer -> parser)
     *
     * i.e. _from_my_key => FromMyKey but from_my_key => fromMyKey
     */
    public function testFromKeyPascalCase(): void
    {
        $strategy = new KeyMappingUnderscore();
        $result = $strategy->from('FromMyKey');
        $this->assertEquals('_from_my_key', $result);
    }

    public function testToKey(): void
    {
        $strategy = new KeyMappingUnderscore();
        $result = $strategy->to('from_my_key');
        $this->assertEquals('fromMyKey', $result);
    }

    public function testToKeyStartingWithUnderscore(): void
    {
        $strategy = new KeyMappingUnderscore();
        $result = $strategy->to('__from_my_key');
        $this->assertEquals('_fromMyKey', $result);
    }

    public function testToKeyPascalCase(): void
    {
        $strategy = new KeyMappingUnderscore();
        $result = $strategy->to('_from_my_key');
        $this->assertEquals('FromMyKey', $result);
    }
}
