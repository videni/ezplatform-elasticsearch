<?php

declare(strict_types=1);

namespace Cabbage;

use Cabbage\Http\Client;
use Cabbage\Http\Message;
use Cabbage\Http\Response;
use Cabbage\SPI\Endpoint;
use RuntimeException;

/**
 * Provides low level API for configuring Elasticsearch server by communicating
 * with it using an HTTP client.
 */
final class Configurator
{
    /**
     * @var \Cabbage\Http\Client
     */
    private $client;

    /**
     * @param \Cabbage\Http\Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param \Cabbage\SPI\Endpoint $endpoint
     *
     * @return \Cabbage\Http\Response
     */
    public function createIndex(Endpoint $endpoint): Response
    {
        $body = [
            'settings' => [
                'number_of_shards' => 1,
                'index.write.wait_for_active_shards' => 1,
            ],
        ];

        $message = new Message(
            (string)json_encode($body),
            [
                'Content-Type' => 'application/json',
            ]
        );

        return $this->client->put($endpoint->getUrl(), $message);
    }

    /**
     * @param \Cabbage\SPI\Endpoint $endpoint
     *
     * @return bool
     */
    public function hasIndex(Endpoint $endpoint): bool
    {
        $message = new Message();

        $response = $this->client->head($endpoint->getUrl(), $message);

        if ($response->status === 200) {
            return true;
        }

        if ($response->status === 404) {
            return false;
        }

        throw new RuntimeException(
            "Invalid response status {$response->status}"
        );
    }

    /**
     * @param \Cabbage\SPI\Endpoint $endpoint
     *
     * @return \Cabbage\Http\Response
     */
    public function deleteIndex(Endpoint $endpoint): Response
    {
        return $this->client->delete($endpoint->getUrl());
    }
}
