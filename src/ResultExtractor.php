<?php

declare(strict_types=1);

namespace Cabbage;

use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;

/**
 * Extracts search result from a HTTP response.
 *
 * @see \Cabbage\Http\Response
 * @see \eZ\Publish\API\Repository\Values\Content\Search\SearchResult
 */
final class ResultExtractor
{
    /**
     * @param string $data
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult
     */
    public function extract(string $data): SearchResult
    {
        $body = json_decode($data);
        $searchHits = [];

        foreach ($body->hits->hits as $hit) {
            $searchHits[] = new SearchHit(['valueObject' => $hit]);
        }

        return new SearchResult([
            'searchHits' => $searchHits,
            'totalCount' => $body->hits->total,
        ]);
    }
}
