<?php

declare(strict_types=1);

namespace Cabbage;

use Cabbage\SPI\Endpoint;
use eZ\Publish\API\Repository\Values\Content\Query;

/**
 * Matches a query to an index.
 */
final class QueryRouter
{
    public function match(Query $query): Endpoint
    {
        return Endpoint::fromDsn('http://localhost:9200/index');
    }
}
