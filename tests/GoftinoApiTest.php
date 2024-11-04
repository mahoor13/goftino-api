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
        $this->api = new GoftinoApi(getenv('GOFTINO_TEST_API_KEY') ?: $this->apiKey);
    }

    public function testChatsThrowsExceptionOnError()
    {
        // Set an invalid API key to trigger an error response
        $this->api->factory('INVALID_API_KEY_TEST');

        // Expect an exception to be thrown when calling chats
        // But Goftino API returns http status code 200 !!! Why? I don't know!
        // $this->expectException(\GuzzleHttp\Exception\RequestException::class);

        $result = $this->api->chats();

        // Assert that the result matches the expected response
        $this->assertIsArray($result);
        $this->assertEquals("error", $result["status"]);
        $this->assertEquals("1", $result["code"]);
    }

    public function testChatsReturnsExpectedData()
    {
        // Call the chats method
        $result = $this->api->chats(limit: 2);

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
     * @depends testChatsReturnsExpectedData
     */
    public function testChatDataReturnsExpectedData($chatId)
    {
        $this->assertNotNull($chatId, 'chat_id is not set from previous test');

        // Call the chats method and set chat_id from testChatsReturnsExpectedData method
        $result = $this->api->chat_data(chat_id: $chatId);

        // Assert that the result matches the expected response
        $this->assertIsArray($result);
        $this->assertEquals("success", $result["status"]);
        $this->assertIsArray($result["data"]);
        $this->assertIsArray($result["data"]["messages"]);
    }
}
