<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Node;
use App\Models\RoomLists;
use App\Models\ConfigGame;
use App\Models\ConfigAttribute;
use App\Models\NodeRoom;

class ServerConfigController extends Controller
{
    public function getNodeList(Request $request)
    {
        $page = (int) $request->get('page', 0);
        $limit = (int) $request->get('limit', 5);
        $platId = (int) $request->get('plat', 0);
        $offset = $page * $limit;
        $node = new Node();
        $node->where('enabled', '1');

        if ($platId > 0) {
            $node->whereRaw("(FIND_IN_SET($platId, plats) or plat = 0)");
        }

        $total = $node->count();
        $res = $node->offset($offset)->limit($limit)->get()->toArray();
        $data = ['node' => (array) $res, 'count' => ceil($total / $limit)];
        $reslut = ['status' => 0, 'data' => $data, 'desc' => 'sucess'];
        return $reslut;
    }

    public function getRoom(Request $request)
    {
        $roomId = (int) $request->get('room_id', 0);
        $roomLists = new RoomLists();
        $room = $roomLists->where('room_id', $roomId)->first();
        $retVal = [];
        if (empty($room)) {
            $retVal['status'] = 10001;
            $retVal['data'] = [];
            $retVal['data']['room_id'] = $roomId;
        } else {
            $room = $room->toArray();
            $retVal['status'] = 0;
            $retVal['data'] = [];
            $retVal['data']['room_id'] = $room['room_id'];
            $retVal['data']['channel_id'] = $room['channel_id'];
            $retVal['data']['uid_tails'] = empty($room['uid_tails']) ? '' : $room['uid_tails'];
            $retVal['data']['max_num'] = $room['max_num'];
            $retVal['data']['status'] = $room['status'];
            $retVal['data']['desc'] = $room['desc'];
            $retVal['data']['last_m_adminid'] = $room['last_m_adminid'];
            $retVal['data']['m_time'] = $room['m_time'];
        }
        return $retVal;
    }

    public function getNodeRoomList(Request $request)
    {
        $nodeRoom = new NodeRoom();
        $nodeRoom = $nodeRoom->leftjoin('node', 'node.id', '=', 'node_room.nodeid');
        $nodeRoom->select(
            'node_room.id',
            'node_room.nodeid',
            'node_room.playid',
            'node_room.roomid',

            'node.gameid',
            'node.sortid',
        );
        $data = $nodeRoom->get()->toArray();
        return ['status' => 0, 'data' => $data, 'desc' => 'success'];
    }
    
    public function getNodeByRoomID(Request $request)
    {
        $roomid = (int) $request->get('roomid', 0);
        $page = (int) $request->get('page', 0);
        $limit = 5;
        $offset = $page * $limit;
        $node = new Node();
        $node = $node->leftJoin('node_room', 'node.id', '=', 'node_room.nodeid');
        $node = $node->where('node_room.roomid', $roomid);
        $nodeCount = $node->count();
        $nodeRoom = $node->select(
            'node.id',
            'node.name',
            'node.sortid',
            'node.mingold',
            'node.maxgold',
            // 'node.tax',
            'node.xmlgame',
            // 'node.roomchargegold',
            'node.bottom',
            'node.enabled',
            'node.gameid',
            'node.tiyan',
            'node.plat',
            'node.plats'
        )
            ->offset($offset)->limit($limit)->get();

        if (!empty($nodeRoom)) {
            $data = ['node' => $nodeRoom->toArray(), 'count' => ceil($nodeCount / $limit)];
            $result = ['status' => 0, 'data' => $data, 'desc' => 'success'];
        } else {
            $result = ['status' => 1, 'data' => [], 'desc' => 'fail'];
        }
        return $result;
    }

    public function getConfigGameList(Request $request)
    {
        $gameid = (int) $request->get('gameid', 0);
        $gameType = (int) $request->get('game_type', '');
        $page = (int) $request->get('page', 0);
        $limit = 3500;
        $count = 0;
        $offset = $page * $limit;
        $configGame = new ConfigGame();
        $game = $configGame->where('game_id', $gameid)->where('game_type', $gameType)->first();
        if (!empty($game)) {
            $game = $game->toArray();
            //游戏配置进行字符串切割（c最大支持4096个字符）
            $game['xml'] = preg_replace('/<!--((?!-->).)*-->/s', '', $game['xml']);
            // $game['xml'] = str_replace([" ", "\n"], '', $game['xml']);
            $str_xml = $game['xml'];
            $str_xml_len = strlen($str_xml);
            if ($str_xml_len > $limit) {
                $count = ceil($str_xml_len / $limit);
                $game['xml'] = (string) substr($str_xml, $offset, $limit);
            }
            $result = ['status' => 0, 'data' => $game, 'count' => $count, 'page' => $page, 'desc' => 'sucess'];
        } else {
            $result = ['status' => 1, 'data' => [], 'count' => 0, 'desc' => 'fail'];
        }

        return $result;
    }

    public function getNodes(Request $request)
    {
        $platId = (int) $request->get('plat', 0);

        $node = new Node();
        $node->where('enabled', '1');

        if ($platId > 0) {
            $node->whereRaw("(FIND_IN_SET($platId, plats) or plat = 0)");
        }

        $total = $node->count();
        $res = $node->select('id', 'mingold', 'maxgold', 'gameid', 'plat', 'plats', 'tiyan', 'tax')->get()->toArray();

        if (!empty($node)) {
            $data = ['node' => $res, 'count' => $total];
            $result = ['status' => 0, 'data' => $data, 'desc' => 'success'];
        } else {
            $data = ['node' => [], 'count' => 0];
            $result = ['status' => 0, 'data' => $data, 'desc' => 'success'];
        }
        return $result;
    }

    public function getGameStatusConfig(Request $request)
    {
        $configAttribute = new ConfigAttribute();
        $list = $configAttribute->where('v_key_name', 'GAME_STATUS')->first()->toArray();
        if (!empty($list)) {
            $result = ['status' => 0, 'data' => json_decode($list['t_key_value'], true), 'desc' => 'success'];
        } else {
            $result = ['status' => 1, 'data' => [], 'desc' => 'fail'];
        }
        return $result;
    }

}
