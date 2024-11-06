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
     * @param  int $log_level
     * 
     * @return void
     */
    public function __construct(
        string $api_key,
        public int $log_level = 0,
    ) {
        $this->factory($api_key);
    }

    /**
     * factory
     *
     * @param  string $api_key
     * 
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
     * @param string $function
     * @param array $params
     * 
     * @return mixed
     */
    public function call(string $function, array $params = []): mixed
    {
        [$request_method, $api] = explode('_', $function, 2);

        if ($this->log_level > 0) {
            echo "\n\nAPI:\t{$request_method} -> {$api}\n";
            echo "Params:\t" . json_encode($params, JSON_UNESCAPED_UNICODE) . "\n";
        }

        $params = array_filter($params, fn($val) => $val !== null, ARRAY_FILTER_USE_BOTH);
        $response = strtolower($request_method) === 'get'
            ? $this->client->get($api, ['query' => $params])
            : $this->client->post($api, ['json' => $params]);

        $result = json_decode($response->getBody()->getContents(), true);

        if ($this->log_level > 0) {
            echo "Result:\t" . json_encode($result, JSON_UNESCAPED_UNICODE) . "\n";
        }

        return $result;
    }

    /**
     * get_chats
     * 
     * @param int $limit
     * @param int $page
     * @param string|null $operator_id
     * @param string|null $status
     * @param bool|null $has_owner
     * 
     * @return mixed
     */
    public function get_chats(int $limit = 50, int $page = 1, ?string $operator_id = null, string $status = null, ?bool $has_owner = null)
    {
        return $this->call(__FUNCTION__, compact('limit', 'page', 'operator_id', 'status', 'has_owner'));
    }

    /**
     * get_chat_data
     *
     * @param string $chat_id
     * @param string|null $from_date
     * @param string|null $to_date
     * @param int $limit
     * @param int $page
     * 
     * @return mixed
     */
    public function get_chat_data(string $chat_id, ?string $from_date = null, ?string $to_date = null, int $limit = 50, int $page = 1)
    {
        return $this->call(__FUNCTION__, compact('chat_id', 'from_date', 'to_date', 'limit', 'page'));
    }

    /**
     * post_send_message
     *
     * @param string $chat_id
     * @param string $operator_id
     * @param string $message
     * @param string|null $reply_id
     * 
     * @return mixed
     */
    public function post_send_message(string $chat_id, string $operator_id, string $message, ?string $reply_id = null)
    {
        return $this->call(__FUNCTION__, compact('chat_id', 'operator_id', 'message', 'reply_id'));
    }

    /**
     * post_send_message_from_user
     *
     * @param string $chat_id
     * @param string $message
     * @param string|null $reply_id
     * 
     * @return mixed
     */
    public function post_send_message_from_user(string $chat_id, string $message, ?string $reply_id = null)
    {
        return $this->call(__FUNCTION__, compact('chat_id', 'message', 'reply_id'));
    }

    /**
     * post_operator_typing
     *
     * @param string $chat_id
     * @param string $operator_id
     * @param string $typing_status
     * 
     * @return mixed
     */
    public function post_operator_typing(string $chat_id, string $operator_id, string $typing_status = 'true')
    {
        return $this->call(__FUNCTION__, compact('chat_id', 'operator_id', 'typing_status'));
    }

    /**
     * post_close_chat
     *
     * @param string $chat_id
     * @param string $operator_id
     * 
     * @return mixed
     */
    public function post_close_chat(string $chat_id, string $operator_id)
    {
        return $this->call(__FUNCTION__, compact('chat_id', 'operator_id'));
    }

    /**
     * post_transfer_chat
     *
     * @param string $chat_id
     * @param string $from_operator
     * @param string $to_operator
     * 
     * @return mixed
     */
    public function post_transfer_chat(string $chat_id, string $from_operator, string $to_operator)
    {
        return $this->call(__FUNCTION__, compact('chat_id', 'from_operator', 'to_operator'));
    }

    /**
     * post_unassign_chat
     *
     * @param string $chat_id
     * @param string $from_operator
     * 
     * @return mixed
     */
    public function post_unassign_chat(string $chat_id, string $from_operator)
    {
        return $this->call(__FUNCTION__, compact('chat_id', 'from_operator'));
    }

    /**
     * get_edit_message
     *
     * @param string|null $message_id
     * 
     * @return mixed
     */
    public function get_edit_message(string $message_id = null)
    {
        return $this->call(__FUNCTION__, compact('message_id'));
    }

    /**
     * post_edit_message
     *
     * @param string $message
     * @param string $message_id
     * 
     * @return mixed
     */
    public function post_edit_message(string $message, string $message_id)
    {
        return $this->call(__FUNCTION__, compact('message', 'message_id'));
    }

    /**
     * post_send_poll
     *
     * @param string $chat_id
     * 
     * @return mixed
     */
    public function post_send_poll(string $chat_id)
    {
        return $this->call(__FUNCTION__, compact('chat_id'));
    }

    /**
     * create_chat
     *
     * @param string $message
     * 
     * @return mixed
     */
    public function post_create_chat(string $message)
    {
        return $this->call(__FUNCTION__, compact('message'));
    }

    /**
     * post_remove_chat
     *
     * @param string $chat_id
     * 
     * @return mixed
     */
    public function post_remove_chat(string $chat_id)
    {
        return $this->call(__FUNCTION__, compact('chat_id'));
    }

    /**
     * get_user_data
     *
     * @param string|null $chat_id
     * @param string|null $user_id
     * 
     * @return mixed
     */
    public function get_user_data(?string $chat_id = null, ?string $user_id = null)
    {
        return $this->call(__FUNCTION__, compact('chat_id', 'user_id'));
    }

    /**
     * post_user_data
     *
     * @param string|null $user_id
     * @param string|null $name
     * @param string|null $phone
     * @param string|null $email
     * @param string|null $avatar
     * @param string|null $description
     * @param array $tags
     * @param mixed 
     * 
     * @return mixed
     */
    public function post_user_data(
        ?string $user_id = null,
        ?string $name = null,
        ?string $phone = null,
        ?string $email = null,
        ?string $avatar = null,
        ?string $description = null,
        array $tags = [],
    ) {
        return $this->call(__FUNCTION__, compact('user_id', 'name', 'phone', 'email', 'avatar', 'description', 'tags'));
    }

    /**
     * post_ban_user
     *
     * @param string $operator_id
     * @param string|null $chat_id
     * @param string|null $user_id
     * 
     * @return mixed
     */
    public function post_ban_user(string $operator_id, ?string $chat_id = null, ?string $user_id = null)
    {
        return $this->call(__FUNCTION__, compact('operator_id', 'chat_id', 'user_id'));
    }

    /**
     * get_user_visited_pages
     *
     * @param string|null $user_id
     * 
     * @return mixed
     */
    public function get_user_visited_pages(?string $user_id = null)
    {
        return $this->call(__FUNCTION__, compact('user_id'));
    }

    /**
     * get_operators
     *
     * @return mixed
     */
    public function get_operators()
    {
        return $this->call(__FUNCTION__);
    }

    public function operator_data(?string $email = null, ?string $operator_id = null)
    {
        return $this->call(__FUNCTION__);
    }
}
