<?php

namespace App\Http\Library;

use App\Models\Node;
use App\Models\AccountExt;
use App\Models\Customer;
use App\Models\TransferInOut;

use Hashids\Hashids;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;

use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
class Server
{
    public function processControlXmlPush($gameId, $gameType)
    {

        $url = env('SERVER_CHALLENGE_URL') . '?cmd=' . '59';
        $data = [
            'game_id' => $gameId,
            'game_type' => $gameType,
        ];
        return $this->post($url, ['data' => $data]);
    }

    public function refreshRoom()
    {
        $url = env('SERVER_CHALLENGE_URL') . '?cmd=' . '56';
        $data = [];
        $data['status'] = 2;
        return $this->post($url, $data);
    }

    public function refreshMaintenanceConfig($data) {
        $url = env('SERVER_CHALLENGE_URL') . '?cmd=' . '79';
        return $this->post($url, ['data' => $data]);
    }

    public function roomNotify($id, $type = 'update')
    {
        $url = env('SERVER_CHALLENGE_URL') . '?cmd=' . '56';
        if ($type == 'delete') {
            $data['status'] = 1;
        } else {
            $data = Node::where('id', $id)->first()->toArray();
            unset($data['optime']);
            $data['status'] = 0;
        }
        return $this->post($url, $data);
    }

    public function updateRoomNotify($data)
    {
        // Log::info('updateRoomNotify', $data);
        $url = env('SERVER_CHALLENGE_URL') . '?cmd=' . '52';
        // $servData = [];
        // $servData['room_id'] = $room['room_id'];
        // $servData['channel_id'] = $room['channel_id'];
        // $servData['status'] = $room['status'];
        // $servData['uid_tails'] = $room['uid_tails'];
        // $servData['max_num'] = $room['max_num'];
        
        // $servData['room_id'] = $oldRoomData['room_id'];
        // $servData['channel_id'] = $oldRoomData['channel_id'];
        // $servData['status'] = $oldRoomData['status'];
        return $this->post($url, ['data' => [$data]]);
    }

    public function getInventory($gameId, $nodeId, $num = 0)
    {
        $url = env('SERVER_CHALLENGE_URL') . '?cmd=' . '73';
        $r = $this->post($url, ['gameid' => $gameId, 'nodeid' => $nodeId, 'num' => $num]);
        return $r;
    }

    public function updateInventory($data)
    {
        $url = env('SERVER_CHALLENGE_URL') . '?cmd=' . '74';
        $r = $this->post($url, $data);
        return $r;
    }

    public function addMoney($data)
    {
        $url = env('SERVER_GAME_URL') . '?cmd=' . '5';
        $r = $this->post($url, $data);
        return $r;
    }

    protected function error($msg, $addtion = '')
    {
        return ['success' => 0, 'result' => $msg, 'addtion' => $addtion];
    }

    protected function success($data)
    {
        return ['success' => 1, 'result' => $data];
    }

    protected function post($url, $data)
    {
        $start = microtime(true);
        $client = new Client([
            'timeout'  => 20,
        ]);

        try {
            $response = $client->request(
                'POST',
                $url,
                [
                    'json' => $data
                ]
            );

            // $response = $client->request('GET', $url);
            $result = $response->getBody()->getContents();
            $arr = json_decode($result, true);
            if (empty($arr)) {
                $cost = intval((microtime(true) - $start) * 1000);
                Log::error('server', [$url, $data, $result, $cost]);
                return $this->error('parser error:' . htmlspecialchars($result));
            }

            $cost = intval((microtime(true) - $start) * 1000);
            Log::info('server', [$url, $data, $result, $cost]);
            return $this->success($arr);
        } catch (ClientException $e) {
            $m1 = Psr7\Message::toString($e->getRequest());
            $m2 = Psr7\Message::toString($e->getResponse());
            $cost = intval((microtime(true) - $start) * 1000);
            Log::error('server', [$url, $data, $m1, $m2, $cost]);
            return $this->error('[' . $url . ']exception#1:' . $m1 . '|' . $m2);
        } catch (GuzzleException $e) {
            $m1 = Psr7\Message::toString($e->getRequest());
            $m2 = $e->getMessage();
            $cost = intval((microtime(true) - $start) * 1000);
            Log::error('server', [$url, $data, $m1, $m2, $cost]);
            return $this->error('[' . $url . ']exception#2:' . $m1 . '|' . $m2);
        }
    }
}
