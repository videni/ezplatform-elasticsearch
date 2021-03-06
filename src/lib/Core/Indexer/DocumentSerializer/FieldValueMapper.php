<?php

declare(strict_types=1);

namespace Cabbage\Core\Indexer\DocumentSerializer;

use Cabbage\Core\Indexer\DocumentSerializer\FieldValueMapper\Visitor;
use Cabbage\SPI\Document\Field;
use RuntimeException;

/**
 * Maps field's value to search engine format.
 *
 * @see \Cabbage\SPI\Field
 */
final class FieldValueMapper
{
    /**
     * A collection of aggregated visitors.
     *
     * @var \Cabbage\Core\Indexer\DocumentSerializer\FieldValueMapper\Visitor[]
     */
    private $visitors = [];

    /**
     * @param \Cabbage\Core\Indexer\DocumentSerializer\FieldValueMapper\Visitor[] $visitors
     */
    public function __construct(array $visitors)
    {
        foreach ($visitors as $visitor) {
            $this->addVisitor($visitor);
        }
    }

    /**
     * Add visitor to the internal collection.
     *
     * @param \Cabbage\Core\Indexer\DocumentSerializer\FieldValueMapper\Visitor $visitor
     */
    private function addVisitor(Visitor $visitor): void
    {
        $this->visitors[] = $visitor;
    }

    /**
     * Map the field's value.
     *
     * @param \Cabbage\SPI\Document\Field $field
     *
     * @return mixed
     */
    public function map(Field $field)
    {
        foreach ($this->visitors as $visitor) {
            if ($visitor->accept($field)) {
                return $visitor->visit($field);
            }
        }

        throw new RuntimeException(
            "Field of type '{$field->type->identifier}' is not handled"
        );
    }
}
