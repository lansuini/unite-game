<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TransferTest extends TestCase
{
    public function getClient()
    {
        return [
            'operator_token' => 'TCd83m2bQ8MNX2iNhAXb8DFyz6CECCQJ',
            'secret_key' => 'AD4ekPmRFR46HAeZ8Zwp7yfnSYR4pJMD',
            'player_name' => 'Test-T-A123',
            'nickname' => 'Test-T-A123',
        ];
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_loginGame()
    {
        $host = env('DOMAIN_H5');
        // $client = $this->getClient();
        $url = "http://{$host}/api/cf/LoginGame?trace_id=ed344c65-8ee5-4bdb-99bb-3fd2ef6fade1";
        $postData = '{
            "operator_token": "TCd83m2bQ8MNX2iNhAXb8DFyz6CECCQJ", 
            "secret_key": "AD4ekPmRFR46HAeZ8Zwp7yfnSYR4pJMD", 
            "operator_player_session": "1", 
            "player_name": "player12b000", 
            "currency": "INR", 
            "nickname": "player0000_nickname"
        }';

        $client = $this->getClient();
        $postData = json_decode($postData, true);
        $postData = array_merge($postData, $client);
        $postData['operator_player_session'] = $client['player_name'];
        $response = $this->postJson($url, $postData);
        $res = $response->decodeResponseJson();
        $this->assertNull($res['error']);

        $session = $res['data']['player_session'];
        $url = "http://{$host}/api/web-lobby/login?player_session={$session}&operator_player_param=&game_id=0";
        $response = $this->get($url);
        $res = $response->decodeResponseJson();
        $this->assertNull($res['error']);
    }

    /**
     * @depends test_loginGame
     */
    public function test_TransferInOut()
    {
        $host = env('DOMAIN_H5');
        $client = $this->getClient();

        $url = "http://{$host}/api/cf/GetPlayerWallet?trace_id=103b0e42-2227-4fed-9814-ca53e050ca11";
        $postData1 = '{
            "operator_token": "TCd83m2bQ8MNX2iNhAXb8DFyz6CECCQJ", 
            "secret_key": "AD4ekPmRFR46HAeZ8Zwp7yfnSYR4pJMD", 
            "player_name": "player12b000"
            }';
        $postData1 = json_decode($postData1, true);
        $postData1 = array_merge($postData1, $client);
        $response = $this->postJson($url, $postData1);
        $res = $response->decodeResponseJson();
        $this->assertNull($res['error']);
        $balance = $res['data']['totalBalance'];

        $postData2 = '{
            "operator_token": "TCd83m2bQ8MNX2iNhAXb8DFyz6CECCQJ", 
            "secret_key": "AD4ekPmRFR46HAeZ8Zwp7yfnSYR4pJMD", 
            "player_name": "player12b000",
            "amount": 100,
            "currency": "INR",
            "transfer_reference": "123456c2e2f7f"
            }';
        $postData2 = json_decode($postData2, true);
        $postData2 = array_merge($postData2, $client);
        $postData2['amount'] = 100;
        $postData2['transfer_reference'] = 'Test-' . date('YmdHis') . uniqid();
        $url = "http://{$host}/api/cf/TransferIn?trace_id=9daecc73-61e7-4be5-a557-37cadc3be2f3";
        $response = $this->postJson($url, $postData2);
        $res = $response->decodeResponseJson();
        $this->assertNull($res['error']);

        $postData2['amount'] = 100;
        $postData2['transfer_reference'] = 'Test-' . date('YmdHis') . uniqid();
        $url = "http://{$host}/api/cf/TransferOut?trace_id=9daecc73-61e7-4be5-a557-37cadc3be2f3";
        $response = $this->postJson($url, $postData2);
        $res = $response->decodeResponseJson();
        $this->assertNull($res['error']);

        $url = "http://{$host}/api/cf/GetPlayerWallet?trace_id=103b0e42-2227-4fed-9814-ca53e050ca11";
        $response = $this->postJson($url, $postData1);
        $res = $response->decodeResponseJson();
        $this->assertNull($res['error']);
        $newBalance = $res['data']['totalBalance'];
        $this->assertTrue($balance == $newBalance);
    }

}
