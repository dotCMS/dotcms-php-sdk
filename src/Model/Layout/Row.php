<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model\Layout;

use Dotcms\PhpSdk\Model\AbstractModel;

class Row extends AbstractModel
{
    /**
     * @param Column[] $columns Array of Column objects
     * @param string|null $styleClass CSS class for styling
     * @param array<string, mixed> $additionalProperties Additional properties
     */
    public function __construct(
        public readonly array $columns,
        public readonly ?string $styleClass = null,
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
                'columns' => $this->columns,
                'styleClass' => $this->styleClass,
            ],
            $this->getAdditionalProperties()
        );
    }
}
