<?php

namespace Mahoor13\GoftinoApi;

use GuzzleHttp\Client;

class GoftinoApi
{
    public const BASE_URL = 'https://api.goftino.com/v1/';
    protected $client;

    /**
     * __construct
     *
     * @param  string $api_key
     * @return void
     */
    public function __construct(string $api_key)
    {
        $this->factory($api_key);
    }

    /**
     * factory
     *
     * @param  string $api_key
     * @return void
     */
    public function factory(string $api_key)
    {
        $this->client = new Client([
            'base_uri' => self::BASE_URL,
            'headers' => [
                'Content-Type' => 'application/json',
                'goftino-key' => $api_key,
            ]
        ]);
    }

    /**
     * filter_params
     *
     * @param  array $params
     * @return array
     */
    protected function filter_params(array $params): array
    {
        return array_filter($params, fn($val) => $val !== null, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * @param int $limit
     * @param string|null $page
     * @param string|null $operator_id
     * @param string|null $status
     * @param bool|null $has_owner
     * 
     * @return mixed
     */
    public function chats(int $limit = 50, ?string $page = null, ?string $operator_id = null, string $status = null, ?bool $has_owner = null)
    {
        $options = $this->filter_params(compact('limit', 'page', 'operator_id', 'status', 'has_owner'));
        $response = $this->client->get('chats', ['query' => $options]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
