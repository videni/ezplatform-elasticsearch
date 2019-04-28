<?php

declare(strict_types=1);

namespace Cabbage\Core\Indexer;

use Cabbage\Core\Indexer\DocumentSerializer\TypedFieldNameGenerator;
use Cabbage\Core\Indexer\DocumentSerializer\FieldValueMapper;
use Cabbage\SPI\Document;

/**
 * Serializes an array of Document objects into a JSON string for bulk indexing.
 *
 * @see \Cabbage\SPI\Document
 */
final class DocumentSerializer
{
    /**
     * @var \Cabbage\Core\Indexer\DocumentSerializer\TypedFieldNameGenerator
     */
    private $fieldTypedNameGenerator;

    /**
     * @var \Cabbage\Core\Indexer\DocumentSerializer\FieldValueMapper
     */
    private $fieldValueMapper;

    /**
     * @param \Cabbage\Core\Indexer\DocumentSerializer\TypedFieldNameGenerator $fieldTypedNameGenerator
     * @param \Cabbage\Core\Indexer\DocumentSerializer\FieldValueMapper $fieldValueMapper
     */
    public function __construct(
        TypedFieldNameGenerator $fieldTypedNameGenerator,
        FieldValueMapper $fieldValueMapper
    ) {
        $this->fieldTypedNameGenerator = $fieldTypedNameGenerator;
        $this->fieldValueMapper = $fieldValueMapper;
    }

    /**
     * @param \Cabbage\SPI\Document $document
     *
     * @return string
     */
    public function serialize(Document $document): string
    {
        $data = [];

        foreach ($document->fields as $field) {
            $fieldName = $this->fieldTypedNameGenerator->generate($field);
            $fieldValue = $this->fieldValueMapper->map($field);

            $data[$fieldName] = $fieldValue;
        }

        return json_encode($data, JSON_THROW_ON_ERROR);
    }
}