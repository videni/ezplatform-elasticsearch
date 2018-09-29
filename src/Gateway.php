<?php

declare(strict_types=1);

namespace Cabbage;

use Cabbage\Http\Client;
use Cabbage\Http\Request;
use Cabbage\Http\Response;
use RuntimeException;

/**
 * Communicates with Elasticsearch server using HTTP client.
 */
final class Gateway
{
    /**
     * @var \Cabbage\Http\Client
     */
    private $client;

    /**
     * @var \Cabbage\DocumentSerializer
     */
    private $documentSerializer;

    /**
     * @param \Cabbage\Http\Client $client
     * @param \Cabbage\DocumentSerializer $documentSerializer
     */
    public function __construct(Client $client, DocumentSerializer $documentSerializer)
    {
        $this->client = $client;
        $this->documentSerializer = $documentSerializer;
    }

    public function createIndex(string $uri, string $name): Response
    {
        $uri = "{$uri}/{$name}";
        $body = [
            'settings' => [
                'number_of_shards' => 1,
                'index.write.wait_for_active_shards' => 1,
            ],
        ];

        $request = new Request(
            (string)json_encode($body),
            [
                'Content-Type' => 'application/json',
            ]
        );

        return $this->client->put($request, $uri);
    }

    public function index(string $uri, string $index, Document $document): Response
    {
        $uri = "{$uri}/{$index}/{$document->type}";

        $request = new Request(
            $this->documentSerializer->serialize($document),
            [
                'Content-Type' => 'application/json',
            ]
        );

        $response = $this->client->post($request, $uri);

        if ($response->status !== 201) {
            throw new RuntimeException("Invalid response status {$response->status}");
        }

        return $response;
    }

    public function find(string $uri, string $index, string $type, string $field, $value): Response
    {
        $uri = "{$uri}/{$index}/{$type}/_search";
        $body = [
            'query' => [
                'term' => [
                    $field => $value,
                ],
            ],
        ];

        $request = new Request(
            (string)json_encode($body),
            [
                'Content-Type' => 'application/json',
            ]
        );

        $response = $this->client->get($request, $uri);

        if ($response->status !== 200) {
            throw new RuntimeException("Invalid response status {$response->status}");
        }

        return $response;
    }

    public function flush(string $uri): Response
    {
        $uri = "{$uri}/_flush";
        $request = new Request();

        return $this->client->post($request, $uri);
    }
}
