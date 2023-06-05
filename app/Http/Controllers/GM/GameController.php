<?php

namespace App\Http\Controllers\GM;

use Illuminate\Http\Request;
use App\Models\Manager\Role;
use App\Models\Manager\Admin;
use App\Models\ConfigAttribute;
use App\Models\Manager\ActionLog;
use App\Models\Node;
use App\Models\NodeRoom;
use App\Models\ConfigGame;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Library\DynamicJsonForm;
use App\Http\Library\Server;
use App\Models\NodeEntrance;
use App\Rules\XML;
use Illuminate\Support\Facades\Redis;

class GameController extends GMController
{
    public function winLoseControlView(Request $request)
    {
        return view('GM/Game/gameWinLoseControlView', [
            'pageTitle' => $this->role->getCurrentPageTitle($request),
            'role' => new Role, 'request' => $request
        ]);
    }

    public function winLoseControlList(Request $request)
    {
        $redis = Redis::connection();
        $redis->select(2);

        $limit = $request->query->get('limit', 20);
        $offset = $request->query->get('offset', 0);
        $sort = $request->query->get('sort', 'id');
        $order = $request->query->get('order', 'desc');
        $name = $request->query->get('name');
        $gameID = $request->query->get('gameid');
        $clientId = $request->query->get('client_id');

        $model = new Node();
        !empty($sort) && $model = $model->orderBy($sort, $order);
        $name && $model = $model->where('name', $name);
        $gameID && $model = $model->where('gameid', $gameID);
        // $model = $model->select('gm_admin.id', 'username', 'gm_role.name', 'is_lock', 'last_login_ip', 'last_update_password_time', 'gm_admin.created');
        // $model = $model->leftjoin('gm_role', 'gm_role.id', '=', 'gm_admin.role_id');

        $model = $model->select(
            'id',
            'parentid',
            // 'alias_id',
            'gameid',
            'name',
            // 'name_mark',
            // 'pic_name',
            // 'plat',
            // 'plats',
            // 'sortid',
            // 'mingold',
            // 'maxgold',
            // 'optime',
            // 'xmlgame',
            // 'roomchargegold',
            'enabled',
            // 'hot_register',
            // 'bottom',
            // 'play_method',
            // 'tiyan',
            // 'tax',
        );
        $total = $model->count();
        $rows = $model->offset($offset)->limit($limit)->get()->toArray();
        foreach ($rows as $k => $v) {
            $v['updated'] = $redis->get("stock_update_time_{$clientId}_{$v['gameid']}_{$v['id']}");
            $v['updated'] = !empty($v['updated']) ? date('Y-m-d H:i:s') : '';
            $v['stock_value'] = $redis->get("stock_value_{$clientId}_{$v['gameid']}_{$v['id']}");
            $v['stock_imp_value'] = $redis->get("stock_imp_value_{$clientId}_{$v['gameid']}_{$v['id']}");
            $rows[$k] = $v;
        }
        return [
            'result' => [],
            'rows' => $rows,
            'success' => 1,
            'total' => $total,
        ];
    }

    public function winLoseControlJSONEdit(Request $request, $clientId, $id)
    {
        $redis = Redis::connection();
        $redis->select(2);

        $model = new Node();
        $nodeData = $model->where('id', $id)->first();
        $redisJson = $redis->get("stock_ctr_{$clientId}_{$nodeData->gameid}_{$nodeData->id}");
        $configData = config('gm.json_form_config.win_lose.' . $nodeData->gameid);
        if (empty($configData)) {
            $configData = config('gm.json_form_config.win_lose.win_lose_control');
        }
        $actionButton = $request->input('actionButton');
        $actionField = $request->input('actionField');

        $jsonDef = json_decode(config("gm.json_form_config.win_lose.{$nodeData->gameid}_def"), true);

        if ($request->query('importJson') == 1) {
            $json = $request->input();
            $json = (new DynamicJsonForm($configData))->startPreprocessing($json);
        } else if ($request->query('exportJson') == 1) {
            $name = "stock_ctr_{$clientId}_{$nodeData->gameid}_{$nodeData->id}" . '.json';
            $headers = [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="'.$name.'"',
            ];
            return response()->make($redisJson, 200, $headers);
        } else if ($request->query('exportJson') == 2) {
            $name = "stock_ctr_{$clientId}_{$nodeData->gameid}_{$nodeData->id}_template" . '.json';
            $headers = [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="'.$name.'"',
            ];
            return response()->make($jsonDef, 200, $headers);
        }    
        else {
            $json = $request->input('json', []);
            // $json = !empty($json) ? $json : json_decode($redisJson, true);
            if (empty($json)) {
                $json =  json_decode($redisJson, true);
                $json = (new DynamicJsonForm($configData))->startPreprocessing($json);
            }
        }

        // if ($request->query('autoFixed') == 1) {
        //     $json = json_decode(config("gm.json_form_config.win_lose.{$nodeData->gameid}_def"), true);
        //     if (empty($json)) {
        //         return 'this game not support json define value config module:' . $nodeData['gameid'];
        //     }
        // }
        if (empty($configData)) {
            return 'this game not support json config module:' . $nodeData['gameid'];
        }

        $v1 = !empty($redisJson) ? '/game/winlosecontrol/json/' . $clientId . '/' . $nodeData->id . '?exportJson=1' : '';
        $v2 = !empty($jsonDef) ? '/game/winlosecontrol/json/' . $clientId . '/' . $nodeData->id . '?exportJson=2' : '';
        if ($actionButton) {
            $forms = (new DynamicJsonForm($configData))->$actionButton($actionField)
            ->fill($json, false)
            ->setExport($v1, $v2)
            ->create();
        } else {
            $forms = (new DynamicJsonForm($configData))->fill($json, false);
            if ($request->method() == 'PATCH') {
                if ($forms->isValid()) {
                    $json = (new DynamicJsonForm($configData))->cancelPreprocessing($json);
                    $redis->set("stock_ctr_{$clientId}_{$nodeData->gameid}_{$nodeData->id}", json_encode($json));
                    $redis->set("stock_update_time_{$clientId}_{$nodeData->gameid}_{$nodeData->id}", time());
                    if ($redisJson != json_encode($json)) {
                        ActionLog::create([
                            'admin_id' => $this->admin->getLoginID($request),
                            'admin_username' => $this->admin->getLoginUsername($request),
                            'browser' => $request->header('User-Agent'),
                            'key' => 'GAME_WINLOSECONTROL_JSON_EDIT',
                            'is_success' => 1,
                            'url' => $request->url(),
                            'ip' => $this->ip($request),
                            'desc' => $id,
                            'target_id' => $id,
                            'before' => $redisJson,
                            'after' => json_encode($json),
                            'params' => json_encode($request->all()),
                            'method' => $request->method()
                        ]);
                    }

                    // $server = new Server();
                    // $server->roomNotify($id);
                    return 'success';
                }
            }
            if (!empty($jsonDef)) {
                // $forms->setType(2);
                $forms->setType(1);
            } else {
                $forms->setType(1);
            }

            $forms = $forms->setExport($v1, $v2)->create();
        }
        return view('GM/Game/gameWinLoseControlJSONView', ['forms' => $forms]);
    }
}
