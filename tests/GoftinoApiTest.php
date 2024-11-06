<?php

namespace Mahoor13\GoftinoApi\Tests;

use PHPUnit\Framework\TestCase;
use Mahoor13\GoftinoApi\GoftinoApi;

class GoftinoApiTest extends TestCase
{
    protected $apiKey = 'GOFTINO TEST API KEY'; // Set api key here. But it's better to set GOFTINO_TEST_API_KEY env
    protected $api;

    protected function setUp(): void
    {
        parent::setUp();
        $this->api = new GoftinoApi(getenv('GOFTINO_TEST_API_KEY') ?: $this->apiKey, 1);
    }

    public function testGetChatsThrowsExceptionOnError()
    {
        // Set an invalid API key to trigger an error response
        $this->api->factory('INVALID_API_KEY_TEST');

        // Expect an exception to be thrown when calling chats
        // But Goftino API returns http status code 200 !!! Why? I don't know!
        // $this->expectException(\GuzzleHttp\Exception\RequestException::class);

        $result = $this->api->get_chats();

        // Assert that the result matches the expected response
        $this->assertIsArray($result);
        $this->assertEquals("error", $result["status"]);
        $this->assertEquals("1", $result["code"]);

        // rollback api key
        $this->api->factory(getenv('GOFTINO_TEST_API_KEY') ?: $this->apiKey);
    }

    public function testGetChatsReturnsExpectedData()
    {
        // Call the chats method
        $result = $this->api->get_chats(limit: 2);

        // Assert that the result matches the expected response
        $this->assertIsArray($result);
        $this->assertEquals("success", $result["status"]);
        $this->assertIsArray($result["data"]);
        $this->assertIsArray($result["data"]["chats"]);
        $this->assertLessThanOrEqual(2, count($result["data"]["chats"]));
        $this->assertEquals(1, $result["data"]["page"]);

        $chatId = $result["data"]["chats"][0]['chat_id'];

        return $chatId;
    }

    /**
     * @depends testGetChatsReturnsExpectedData
     */
    public function testGetChatDataReturnsExpectedData($chatId)
    {
        $this->assertNotNull($chatId, 'chat_id is not set from previous test (chats)');

        // Call the chat_data method and set chat_id from testGetChatsReturnsExpectedData method
        $result = $this->api->get_chat_data(chat_id: $chatId);

        // Assert that the result matches the expected response
        $this->assertIsArray($result);
        $this->assertEquals("success", $result["status"]);
        $this->assertIsArray($result["data"]);
        $this->assertIsArray($result["data"]["messages"]);
    }

    public function testPostCreateChatReturnsExpectedData()
    {
        $result = $this->api->post_create_chat("Message 1 (create chat)");

        // Assert that the result matches the expected response
        $this->assertIsArray($result);
        $this->assertEquals("success", $result["status"]);
        $this->assertIsArray($result["data"]);
        $this->assertIsString($result["data"]["chat_id"]);
        $this->assertIsString($result["data"]["user_id"]);

        return $result["data"];
    }

    /**
     * @depends testPostCreateChatReturnsExpectedData
     */
    public function testPostSendMessageFromUserReturnsExpectedData(array $data)
    {
        $this->assertNotNull($data['chat_id'], 'chat_id is not set from previous test (create_chat)');
        $result = $this->api->post_send_message_from_user($data['chat_id'], "Message 2 (send message from user)");

        // Assert that the result matches the expected response
        $this->assertIsArray($result);
        $this->assertEquals("success", $result["status"]);
        $this->assertIsArray($result["data"]);
        $this->assertIsString($result["data"]["message_id"]);
        $data['message_id'] = $result["data"]["message_id"];

        return $data;
    }

    /**
     * @depends testPostSendMessageFromUserReturnsExpectedData
     */
    public function testPostSendMessageReturnsExpectedData(array $data)
    {
        $this->assertNotNull($data['chat_id'], 'user_id is not set from previous test (create_chat)');
        $result = $this->api->post_send_message($data['chat_id'], "671f20cb03abe02f50018dff",  "Hi there 👋", $data['message_id'] ?? null);

        // Assert that the result matches the expected response
        $this->assertIsArray($result);
        $this->assertEquals("success", $result["status"]);
        $this->assertIsArray($result["data"]);
        $this->assertIsString($result["data"]["message_id"]);
    }

    public function testGetOperatorsReturnsExpectedData()
    {
        $result = $this->api->get_operators();

        // Assert that the result matches the expected response
        $this->assertIsArray($result);
        $this->assertEquals("success", $result["status"]);
        $this->assertIsArray($result["data"]);
        $this->assertIsArray($result["data"]["operators"]);
    }
}
