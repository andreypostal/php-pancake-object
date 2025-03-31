<?php /** @noinspection PhpUnitAnnotationToAttributeInspection */

namespace Andrey\PancakeObject;

use Throwable;

/**
 * @codeCoverageIgnore
 */
readonly class Payload
{
    public function __construct(
        public mixed $data = null,
        public bool $skipped = false,
        public ?Throwable $error = null,
    ) {
    }
}
