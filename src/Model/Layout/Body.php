<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model\Layout;

use Dotcms\PhpSdk\Model\AbstractModel;

class Body extends AbstractModel
{
    /**
     * @param Row[] $rows Array of rows
     * @param array<string, mixed> $additionalProperties Additional properties
     */
    public function __construct(
        public readonly array $rows = [],
        array $additionalProperties = [],
    ) {
        $this->setAdditionalProperties($additionalProperties);
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_merge(
            [
                'rows' => $this->rows,
            ],
            $this->getAdditionalProperties()
        );
    }
}
