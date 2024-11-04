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
     * call
     * 
     * @param string $api
     * @param string $request_method
     * @param array $options
     * 
     * @return mixed
     */
    public function call(string $api, string $request_method = 'get', array $options = []): mixed
    {
        $options = array_filter($options, fn($val) => $val !== null, ARRAY_FILTER_USE_BOTH);
        $response = $request_method === 'get'
            ? $this->client->get($api, ['query' => $options])
            : $this->client->post($api, ['json' => $options]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * chats
     * 
     * @param int $limit
     * @param int $page
     * @param string|null $operator_id
     * @param string|null $status
     * @param bool|null $has_owner
     * 
     * @return mixed
     */
    public function chats(int $limit = 50, int $page = 1, ?string $operator_id = null, string $status = null, ?bool $has_owner = null)
    {
        return $this->call(__FUNCTION__, 'get', compact('limit', 'page', 'operator_id', 'status', 'has_owner'));
    }

    /**
     * chat_data
     *
     * @param string $chat_id
     * @param string|null $from_date
     * @param string|null $to_date
     * @param int $limit
     * @param int $page
     * 
     * @return mixed
     */
    public function chat_data(string $chat_id, ?string $from_date = null, ?string $to_date = null, int $limit = 50, int $page = 1)
    {
        return $this->call(__FUNCTION__, 'get', compact('chat_id', 'from_date', 'to_date', 'limit', 'page'));
    }
}
