<?php

declare(strict_types=1);

namespace Cabbage;

/**
 * Represents a document to be indexed in the Elasticsearch backed.
 */
final class Document
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var \Cabbage\Field[]
     */
    public $fields;

    /**
     * @param string $type
     * @param \Cabbage\Field[] $fields
     */
    public function __construct(string $type, array $fields)
    {
        $this->type = $type;
        $this->fields = $fields;
    }
}
