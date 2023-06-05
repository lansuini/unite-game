<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SingleTest extends TestCase
{
    public function getPlayer(): string
    {
        return 'TEST-A123456';
    }

    /**
     * 
     */
    public function test_login(): void
    {
        $host = env('DOMAIN_H5');
        $player = $this->getPlayer();
        $url = "http://{$host}/api/web-lobby/login?operator_token=tongits&operator_player_session={$player}&operator_player_param=&game_id=0";
        $response = $this->call('GET', $url);
        // dd($response->getContent());
        $res = $response->decodeResponseJson();
        $this->assertNull($res['error']);
        $response->assertStatus(200);


        for ($i = 0; $i < 30; $i++) {
            $player = 'TEST-A123456-' . $i;
            $url = "http://{$host}/api/web-lobby/login?operator_token=tongits&operator_player_session={$player}&operator_player_param=&game_id=0";
            $response = $this->call('GET', $url);
            $res = $response->decodeResponseJson();
            $this->assertNull($res['error']);
            $response->assertStatus(200);
        }
    }

    /**
     * 
     * @depends test_login
     */
    public function test_cashget(): void
    {
        $player = $this->getPlayer();
        $account = \App\Models\Account::where('player_name', $player)->first();
        $uid = $account->uid;
        $host = env('DOMAIN_SERVER');
        $url = "http://{$host}/api/cash/get?uid={$uid}&game_id=0";
        $response = $this->call('GET', $url);
        $res = $response->decodeResponseJson();
        $this->assertTrue($res['status'] == 0);
        $this->assertTrue(isset($res['data']) && isset($res['data']['balance_amount']));
    }

    /**
     * 
     * @depends test_login
     */
    public function test_verifySession(): void
    {
        $player = $this->getPlayer();
        $account = \App\Models\Account::where('player_name', $player)->first();
        $uid = $account->uid;
        $host = env('DOMAIN_SERVER');
        $url = "http://{$host}/api/verifySession?uid={$uid}&game_id=0";
        $response = $this->call('GET', $url);
        $res = $response->decodeResponseJson();
        $this->assertTrue($res['status'] == 0);
    }

    public function getData($uid, $balanace, $transferAmount)
    {
        $r = '{"api_mode":0,"bet_amount":100,"bet_id":"0","bill_type":54,
            "client_id":4,"game_id":4440,"is_end":1,"last_gold":10000000,
            "now_gold":9999900,"parent_bet_id":"B6-88-D2-88-FE-A3-4B-53-9B-29-E7-0A-31-3F-4D-3F",
            "player_uid":200506,"status":0,"token":"",
            "transaction_id":"{0}-{B6-88-D2-88-FE-A3-4B-53-9B-29-E7-0A-31-3F-4D-3F}-{54}-{bd88762378add03c7b0472bb4dbebb15}",
            "transfer_amount":-100}';

        $data = json_decode($r, true);
        $data['transaction_id'] = 'PHPUnit-id-' . uniqid();
        $data['parent_bet_id'] = 'PHPUnit-parent-' . uniqid();
        // $data['balanceAfter'] = $data['last_gold'];
        // $data['balanceBefore'] = $data['now_gold'];

        $data['balanceAfter'] = $balanace + $transferAmount;
        $data['balanceBefore'] = $balanace;
        $data['player_uid'] = $uid;
        $data['client_id'] = 0;
        $data['transfer_amount'] = $transferAmount;
        $data['create_time'] = date('Y-m-d H:i:s');
        if ($transferAmount > 0) {
            $data['bill_type'] = 70;
        } else {
            $data['bill_type'] = 54;
        }
        $transferInOut = new \App\Models\TransferInOut;
        $res = $transferInOut->setTable('transfer_inout_4')->create($data);
        // dd($res);
        return $data;
    }

    /**
     * 
     * @depends test_cashget
     */
    public function test_TransferInOut(): void
    {
        $player = $this->getPlayer();
        $account = \App\Models\Account::where('player_name', $player)->first();
        $uid = $account->uid;
        $host = env('DOMAIN_SERVER');

        // cost
        $url = "http://{$host}/api/cash/get?uid={$uid}&game_id=0";
        $response = $this->call('GET', $url);
        $res = $response->decodeResponseJson();
        $balanace = $res['data']['balance_amount'];

        $transferAmount = -100;
        $postData = $this->getData($uid, $balanace, $transferAmount);
        $url = "http://{$host}/api/cash/transferInOut";
        $response = $this->postJson($url, $postData);
        $res = $response->decodeResponseJson();
        // dd([$postData, $res]);
        $this->assertTrue($res['status'] == 0);
        $transferInOut = new \App\Models\TransferInOut;
        $t = $transferInOut->setTable('transfer_inout_4')->where('transaction_id', $postData['transaction_id'])->first();
        $this->assertTrue($t->status == 1);

        $url = "http://{$host}/api/cash/get?uid={$uid}&game_id=0";
        $response = $this->call('GET', $url);
        $res = $response->decodeResponseJson();
        $this->assertTrue(($balanace + $transferAmount) == $res['data']['balance_amount']);

        // add
        $url = "http://{$host}/api/cash/get?uid={$uid}&game_id=0";
        $response = $this->call('GET', $url);
        $res = $response->decodeResponseJson();
        $balanace = $res['data']['balance_amount'];

        $transferAmount = 100;
        $postData = $this->getData($uid, $balanace, $transferAmount);
        $url = "http://{$host}/api/cash/transferInOut";
        $response = $this->postJson($url, $postData);
        $res = $response->decodeResponseJson();
        $this->assertTrue($res['status'] == 0);
        $transferInOut = new \App\Models\TransferInOut;
        $t = $transferInOut->setTable('transfer_inout_4')->where('transaction_id', $postData['transaction_id'])->first();
        $this->assertTrue($t->status == 1);

        $url = "http://{$host}/api/cash/get?uid={$uid}&game_id=0";
        $response = $this->call('GET', $url);
        $res = $response->decodeResponseJson();
        $this->assertTrue(($balanace + $transferAmount) == $res['data']['balance_amount']);
    }
}
