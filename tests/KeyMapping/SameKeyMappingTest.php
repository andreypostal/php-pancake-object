<?php
namespace KeyMapping;

use Andrey\PancakeObject\KeyMapping\SameKeyMapping;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SameKeyMapping::class)]
final class SameKeyMappingTest extends TestCase
{
    public function testFromKey(): void
    {
        $strategy = new SameKeyMapping();
        $result = $strategy->from('fromMyKey');
        $this->assertEquals('fromMyKey', $result);
    }

    public function testToKey(): void
    {
        $strategy = new SameKeyMapping();
        $result = $strategy->to('from_my_key');
        $this->assertEquals('from_my_key', $result);
    }
}
