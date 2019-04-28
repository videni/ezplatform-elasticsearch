<?php

declare(strict_types=1);

namespace Cabbage\SPI;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use eZ\Publish\SPI\Persistence\Content\ContentInfo;

abstract class Searcher
{
    /**
     * @see \eZ\Publish\SPI\Search\Handler::findContent()
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Query $query
     * @param \Cabbage\SPI\LanguageFilter $languageFilter
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult
     */
    abstract public function findContent(Query $query, LanguageFilter $languageFilter): SearchResult;

    /**
     * @see \eZ\Publish\SPI\Search\Handler::findSingle()
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Query\Criterion $filter
     * @param \Cabbage\SPI\LanguageFilter $languageFilter
     *
     * @return \eZ\Publish\SPI\Persistence\Content\ContentInfo
     */
    abstract public function findSingle(Criterion $filter, LanguageFilter $languageFilter): ContentInfo;

    /**
     * @see \eZ\Publish\SPI\Search\Handler::findLocations()
     *
     * @param \eZ\Publish\API\Repository\Values\Content\LocationQuery $query
     * @param \Cabbage\SPI\LanguageFilter $languageFilter
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult
     */
    abstract public function findLocations(LocationQuery $query, LanguageFilter $languageFilter): SearchResult;

    /**
     * @see \eZ\Publish\SPI\Search\Handler::suggest()
     *
     * @param string $prefix
     * @param string[] $fieldPaths
     * @param int $limit
     * @param \eZ\Publish\API\Repository\Values\Content\Query\Criterion $filter
     */
    abstract public function suggest($prefix, $fieldPaths = [], $limit = 10, ?Criterion $filter = null): void;
}