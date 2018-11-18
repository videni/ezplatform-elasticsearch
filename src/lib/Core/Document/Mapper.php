<?php

declare(strict_types=1);

namespace Cabbage\Core\Document;

use Cabbage\Core\Document\Mapper\ContentField\Mapper as ContentFieldMapper;
use Cabbage\Core\Document\Mapper\IdGenerator;
use Cabbage\SPI\Document;
use Cabbage\SPI\Document\Field;
use Cabbage\SPI\Document\Field\Type\Identifier;
use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Content\Location;
use eZ\Publish\SPI\Persistence\Content\Location\Handler as LocationHandler;
use eZ\Publish\SPI\Persistence\Content\Type;
use eZ\Publish\SPI\Persistence\Content\Type\Handler as TypeHandler;

/**
 * Maps eZ Platform Content to an array of Document instances.
 *
 * @see \eZ\Publish\SPI\Persistence\Content
 * @see \Cabbage\SPI\Document
 */
final class Mapper
{
    /**
     * @var \eZ\Publish\SPI\Persistence\Content\Location\Handler
     */
    private $locationHandler;

    /**
     * @var \eZ\Publish\SPI\Persistence\Content\Type\Handler
     */
    private $typeHandler;

    /**
     * @var \Cabbage\Core\Document\Mapper\ContentField\Mapper
     */
    private $contentFieldMapper;

    /**
     * @var \Cabbage\Core\Document\Mapper\IdGenerator
     */
    private $idGenerator;

    /**
     * @param \eZ\Publish\SPI\Persistence\Content\Location\Handler $locationHandler
     * @param \eZ\Publish\SPI\Persistence\Content\Type\Handler $typeHandler
     * @param \Cabbage\Core\Document\Mapper\ContentField\Mapper $contentFieldMapper
     * @param \Cabbage\Core\Document\Mapper\IdGenerator $idGenerator
     */
    public function __construct(
        LocationHandler $locationHandler,
        TypeHandler $typeHandler,
        ContentFieldMapper $contentFieldMapper,
        IdGenerator $idGenerator
    ) {
        $this->locationHandler = $locationHandler;
        $this->typeHandler = $typeHandler;
        $this->contentFieldMapper = $contentFieldMapper;
        $this->idGenerator = $idGenerator;
    }

    /**
     * @param \eZ\Publish\SPI\Persistence\Content $content
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     *
     * @return \Cabbage\SPI\Document[]
     */
    public function map(Content $content): array
    {
        $documents = [];
        $type = $this->typeHandler->load($content->versionInfo->contentInfo->contentTypeId);
        $locations = $this->locationHandler->loadLocationsByContent(
            $content->versionInfo->contentInfo->id
        );

        $documents[] = $this->mapContent($content, $type);

        foreach ($locations as $location) {
            $documents[] = $this->mapLocation($location, $content, $type);
        }

        return $documents;
    }

    /**
     * @param \eZ\Publish\SPI\Persistence\Content $content
     * @param \eZ\Publish\SPI\Persistence\Content\Type $type
     *
     * @return \Cabbage\SPI\Document
     */
    private function mapContent(Content $content, Type $type): Document
    {
        $fieldsGrouped = [[]];

        $contentMetadataFields = [
            new Field(
                'content_id',
                $content->versionInfo->contentInfo->id,
                new Identifier()
            ),
        ];

        $fieldsGrouped[] = $contentMetadataFields;
        $fieldsGrouped[] = $this->contentFieldMapper->map($content, $type);

        return new Document(
            $this->idGenerator->generateContentDocumentId($content),
            Document::TypeContent,
            array_merge(...$fieldsGrouped)
        );
    }

    /**
     * @param \eZ\Publish\SPI\Persistence\Content\Location $location
     * @param \eZ\Publish\SPI\Persistence\Content $content
     * @param \eZ\Publish\SPI\Persistence\Content\Type $type
     *
     * @return \Cabbage\SPI\Document
     */
    private function mapLocation(Location $location, Content $content, Type $type): Document
    {
        $fieldsGrouped = [[]];

        $contentMetadataFields = [
            new Field(
                'content_id',
                $content->versionInfo->contentInfo->id,
                new Identifier()
            ),
        ];

        $locationMetadataFields = [
            new Field(
                'location_id',
                $location->id,
                new Identifier()
            ),
        ];

        $fieldsGrouped[] = $contentMetadataFields;
        $fieldsGrouped[] = $locationMetadataFields;
        $fieldsGrouped[] = $this->contentFieldMapper->map($content, $type);

        return new Document(
            $this->idGenerator->generateLocationDocumentId($location),
            Document::TypeLocation,
            array_merge(...$fieldsGrouped)
        );
    }
}
