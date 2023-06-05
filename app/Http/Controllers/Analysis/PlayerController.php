<?php

namespace App\Http\Controllers\Analysis;

use Illuminate\Http\Request;
// use App\Http\Controllers\GM\AdminController as Controller;
use App\Models\Account;
use App\Models\Logon;
use App\Models\VersusList;
use App\Models\Gold;
use App\Models\UserRoomExt;
use App\Models\UserRoom;
use App\Models\UserHall;
use App\Models\Node;
use App\Models\AccountsToday;
use App\Models\DataReport;
use App\Models\DataReportSub;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class PlayerController extends AnalysisController
{
    protected $connection = 'tongits';

    public function accountView(Request $request)
    {
        return view('Analysis/Player/accountView', [
            'pageTitle' => $this->role->getCurrentPageTitle($request),
            'request' => $request
        ]);
    }

    public function accountList(Request $request)
    {
        $limit = $request->query->get('limit', 20);
        $offset = $request->query->get('offset', 0);
        $sort = $request->query->get('sort', 'id');
        $order = $request->query->get('order', 'desc');
        $v = $request->query->get('v');
        $t = $request->query->get('t');
        $t2 = $request->query->get('t2');

        $isRiskUser = $request->query->get('is_risk_user');
        $bannedType = $request->query->get('banned_type');
        $accountType = $request->query->get('account_type');
        $clientId = $request->query->get('client_id');
        $clientIdSub = $request->query->get('client_id_sub');
        $created = $request->query->get('created');
        $created = urldecode($created);
        $created = explode(' - ', $created);
        $s  = \DateTime::createFromFormat('m/d/Y', $created[0])->format('Y-m-d 00:00:00');
        $e  = \DateTime::createFromFormat('m/d/Y', $created[1])->format('Y-m-d 23:59:59');

        $redis = Redis::connection();

        $vms = [
            'uid' => 'account.uid',
            'player_name' => 'account.player_name',
            'nickname' => 'account.nickname',
        ];

        $sorts = [
            'uid' => 'account.uid',
            'last_logon_time' => 'account_ext.last_logon_time',
            'created' => 'account.created',
        ];

        $model = new Account();
        !empty($sort) && $model = $model->orderBy($sorts[$sort], $order);
        $isRiskUser && $model = $model->where('account_ext.is_risk_user', $isRiskUser);
        $accountType && $model = $model->where('account.account_type', $accountType);
        $bannedType && $model = $model->where('account.banned_type', $bannedType);
        $clientId && $model = $model->where('account_ext.client_id', $clientId);
        $clientIdSub && $model = $model->where('account_ext.client_id_sub', $clientIdSub);
        if (empty($v) && $t2 == 'created') {
            $model = $model->where('account.created', '>=', $s);
            $model = $model->where('account.created', '<=', $e);
        } else if (empty($v)) {
            $model = $model->where('account_ext.last_logon_time', '>=', $s);
            $model = $model->where('account_ext.last_logon_time', '<=', $e);
        }

        // $model = $model->select('gm_admin.id', 'username', 'gm_role.name', 'is_lock', 'last_login_ip', 'last_update_password_time', 'gm_admin.created');
        // $model = $model->leftjoin('gm_role', 'gm_role.id', '=', 'gm_admin.role_id');
        if (isset($vms[$t]) && !empty($v)) {
            $model = $model->where($vms[$t], $v);
            // dd($vms, $vms[$t], $v);
        }

        $model = $model->select(
            'account.uid',
            'account.player_name',
            'account.nickname',
            'account.avatar',
            'account.account_type',
            'account.banned_time',
            'account.banned_type',
            'account.created',
            'account_ext.client_id',
            'account_ext.client_id_sub',
            'account_ext.is_risk_user',
            'account_ext.last_logon_time',
        );

        $model = $model->leftjoin('account_ext', 'account_ext.uid', '=', 'account.uid');

        $total = $model->count();
        $rows = $model->offset($offset)->limit($limit)->get()->toArray();

        foreach ($rows as $k => $v) {
            $v['balance'] = (int) $redis->hget($v['uid'], 'gold');
            $rows[$k] = $v;
        }
        return [
            'result' => [],
            'rows' => $rows,
            'success' => 1,
            'total' => $total,
            // 's' => $s->format('Y-m-d'),
            // 's1' => $created[0],
        ];
    }

    public function accountDetail(Request $request, $uid)
    {
        $model = new Account();
        $model->where('account.uid', '=', $uid);
        $model = $model->select(
            'account.uid',
            'account.player_name',
            'account.nickname',
            'account.avatar',
            'account.account_type',
            'account.banned_time',
            'account.banned_type',
            'account.created',

            'account_ext.is_risk_user',
            'account_ext.last_logon_time',
        );

        $model = $model->leftjoin('account_ext', 'account_ext.uid', '=', 'account.uid');
        $data = $model->first();

        return $this->success($data);
    }

    public function accountEdit(Request $request, $uid)
    {
    }

    public function loginLogView(Request $request)
    {
        return view('Analysis/Player/loginLogView', [
            'pageTitle' => $this->role->getCurrentPageTitle($request),
            'request' => $request
        ]);
    }

    public function loginLogList(Request $request)
    {
        $limit = $request->query->get('limit', 20);
        $offset = $request->query->get('offset', 0);
        $sort = $request->query->get('sort', 'id');
        $order = $request->query->get('order', 'desc');

        $uid = $request->query->get('uid');
        $ip = $request->query->get('ip');
        $clientId = $request->query->get('client_id');
        $gameId = $request->query->get('game_id');

        $created = $request->query->get('created');
        $created = urldecode($created);
        $created = explode(' - ', $created);
        $s  = \DateTime::createFromFormat('m/d/Y H:i:s', $created[0] . ' 00:00:00')->getTimestamp();
        $e  = \DateTime::createFromFormat('m/d/Y H:i:s', $created[1] . ' 23:59:59')->getTimestamp();

        $model = new Logon();
        !empty($sort) && $model = $model->orderBy($sort, $order);
        $uid && $model = $model->where('uid', $uid);
        $ip && $model = $model->where('ip', $ip);
        $clientId && $model = $model->where('client_id', $clientId);
        $gameId && $model = $model->where('game_id', $gameId);

        $model = $model->where('post_time', '>=', $s);
        $model = $model->where('post_time', '<=', $e);

        $model = $model->select(
            'id',
            'post_time',
            'uid',
            'client_id',
            'client_id_sub',
            'version',
            'os',
            'os_version',
            'sp_id',
            'brand',
            'model',
            'imsi',
            'imei',
            'ip',
            'game_id',
            'pack_version',
            // 'third_party',
            // 'third_nick',
        );

        $total = $model->count();
        $rows = $model->offset($offset)->limit($limit)->get()->toArray();
        return [
            'result' => [],
            'rows' => $rows,
            'success' => 1,
            'total' => $total,
            // 's' => $s->format('Y-m-d'),
            // 's1' => $created[0],
            's' => $s,
            'e' => $e,
        ];
    }

    public function playLogView(Request $request)
    {
        return view('Analysis/Player/playLogView', [
            'pageTitle' => $this->role->getCurrentPageTitle($request),
            'request' => $request
        ]);
    }

    public function playLogList(Request $request)
    {
        DB::connection('Master')->enableQueryLog();
        $limit = $request->query->get('limit', 20);
        $offset = $request->query->get('offset', 0);
        $sort = $request->query->get('sort', 'id');
        $order = $request->query->get('order', 'desc');

        $uid = $request->query->get('uid');
        $pid = $request->query->get('pid');
        $gameId = $request->query->get('game_id');
        // $clientId = $request->query->get('client_id');

        $created = $request->query->get('created');
        $created = urldecode($created);
        $created = explode(' - ', $created);
        $s  = \DateTime::createFromFormat('m/d/Y', $created[0])->format('Y-m-d 00:00:00');
        $e  = \DateTime::createFromFormat('m/d/Y', $created[1])->format('Y-m-d 23:59:59');


        $model = new VersusList();
        !empty($sort) && $model = $model->orderBy($sort, $order);
        $uid && $model = $model->where('versus_list.uid', $uid);
        $pid && $model = $model->where('versus_list.pid', $pid);
        // $clientId && $model = $model->where('client_id', $clientId);
        $gameId && $model = $model->where('versus.game_id', $gameId);
        $model = $model->where('versus.post_time', '>=', $s);
        $model = $model->where('versus.post_time', '<=', $e);

        $model = $model->where('versus_list.create_time', '>=', $s);
        $model = $model->where('versus_list.create_time', '<=', $e);

        $model = $model->select(
            'versus_list.id',
            'versus_list.pid',
            'versus_list.uid',
            'versus_list.nickname',
            'versus_list.client_id',
            'versus_list.result',
            'versus_list.score1',
            'versus_list.score2',
            'versus_list.point1',
            'versus_list.point2',
            'versus_list.version',
            'versus_list.user_type',
            'versus_list.create_time',

            'versus.post_time',
            'versus.players',
            'versus.revenue',
            'versus.duration',
            'versus.game_detail',
            'versus.game_id',
            'versus.node_id',
            'node.name',
        );

        $model->leftjoin('versus', 'versus_list.pid', '=', 'versus.id');
        $model->leftjoin('node', 'node.id', '=', 'versus.node_id');

        $total = $model->count();
        $rows = $model->offset($offset)->limit($limit)->get()->toArray();
        return [
            'result' => [],
            'rows' => $rows,
            'success' => 1,
            'total' => $total,
            'db' => DB::connection('Master')->getQueryLog()
        ];
    }

    public function playLogDetailList(Request $request)
    {
        $pid = $request->query->get('pid');
        $uid = $request->query->get('uid');

        $model = new VersusList();
        // $uid && $model = $model->where('versus_list.uid', $uid);
        $model = $model->where('versus_list.pid', $pid);


        $model = $model->select(
            'versus_list.id',
            'versus_list.pid',
            'versus_list.uid',
            'versus_list.nickname',
            'versus_list.client_id',
            'versus_list.result',
            'versus_list.score1',
            'versus_list.score2',
            'versus_list.point1',
            'versus_list.point2',
            'versus_list.version',
            'versus_list.user_type',
            'versus_list.create_time',
            'versus_list.poker_detail',

            'versus.post_time',
            'versus.players',
            'versus.revenue',
            'versus.duration',
            'versus.game_detail',
            'versus.game_id',
            'versus.node_id',
            'node.name',
        );

        $model->leftjoin('versus', 'versus_list.pid', '=', 'versus.id');
        $model->leftjoin('node', 'node.id', '=', 'versus.node_id');

        $total = $model->count();
        $rows = $model->get()->toArray();
        return [
            'result' => [],
            'rows' => $rows,
            'success' => 1,
            'total' => $total,
        ];
    }

    public function goldLogView(Request $request)
    {
        return view('Analysis/Player/goldLogView', [
            'pageTitle' => $this->role->getCurrentPageTitle($request),
            'request' => $request
        ]);
    }

    public function goldLogList(Request $request)
    {
        $limit = $request->query->get('limit', 20);
        $offset = $request->query->get('offset', 0);
        $sort = $request->query->get('sort', 'id');
        $order = $request->query->get('order', 'desc');

        $uid = $request->query->get('uid');
        $id = $request->query->get('id');
        $typeId = $request->query->get('type_id');
        $gameId = $request->query->get('game_id');
        $created = $request->query->get('created');
        $created = urldecode($created);
        $created = explode(' - ', $created);

        $s  = \DateTime::createFromFormat('m/d/Y H:i A', $created[0])->format('Y-m-d H:i:s');
        $e  = \DateTime::createFromFormat('m/d/Y H:i A', $created[1])->format('Y-m-d H:i:s');
        // return [$created, $s, $e];

        $model = new Gold();
        !empty($sort) && $model = $model->orderBy($sort, $order);
        $uid && $model = $model->where('uid', $uid);
        $id && $model = $model->where('id', $id);
        $typeId && $model = $model->where('type_id', $typeId);
        $gameId && $model = $model->where('game_id', $gameId);
        $model = $model->where('post_time', '>=', $s);
        $model = $model->where('post_time', '<=', $e);

        $model = $model->select(
            'id',
            'post_time',
            'uid',
            'client_id',
            'game_id',
            'node_id',
            'type_id',
            'type_id_sub',
            'quantity1',
            'quantity2',
            'quantity',
            'room_id',
            'version',
            'kind_id',
        );

        $total = $model->count();
        $rows = $model->offset($offset)->limit($limit)->get()->toArray();
        return [
            'result' => [],
            'rows' => $rows,
            'success' => 1,
            'total' => $total,
            // 's' => $s->format('Y-m-d'),
            // 's1' => $created[0],
        ];
    }

    public function realOnlinePlayView(Request $request)
    {
        return view('Analysis/Player/realOnlinePlayView', [
            'pageTitle' => $this->role->getCurrentPageTitle($request),
            'request' => $request
        ]);
    }

    public function realOnlinePlayList(Request $request)
    {
        $userHall = new UserHall();
        // $userRoomExt = new UserRoomExt();
        $userRoom = new UserRoom();
        $gameId = $request->query->get('game_id', 0);
        $date = $request->query->get('created');
        $gameId && $userRoom = $userRoom->where('game_id', $gameId);
        $today = \DateTime::createFromFormat('m/d/Y', $date)->format('Y-m-d');
        $yersterday = \DateTime::createFromFormat('m/d/Y', $date)
            ->sub(new \DateInterval('P1D'))
            ->format('Y-m-d');
        $xAxis = $userHall->genTimes();
        $defaultOpt = [
            'tooltip' => ['trigger' => 'axis'],
            'xAxis' => ['data' => $xAxis],
            'yAxis' => ["type" => 'value'],
            'series' => [],
        ];

        $onlineOpt = $defaultOpt;
        $onlineOpt['title']['text'] = __('ts.Real-Online');
        $onlineOpt['legend']['data'] = [$today . ' ' . 'Online'];
        $onlineOpt['series'] = [[
            'name' => $today . ' ' . __('ts.Online'),
            'type' => 'line',
            'data' => $userHall->getHallData($userHall, $today),
        ]];

        $playOpt = $defaultOpt;
        $playOpt['title']['text'] = __('ts.Real-Play');
        $playOpt['legend']['data'] = [$today . ' ' . 'Play'];
        $playOpt['series'] = [[
            'name' => $today . ' ' . __('ts.Play'),
            'type' => 'line',
            'data' => $userHall->getRoomData($userRoom, $today),
        ]];

        $rows = [];
        $rows[] = [
            'r1' => __('ts.Highest yesterday') . "($yersterday)",
            'r2' => $userHall->getMaxHallNum($userHall, $yersterday),
            'r3' => $userHall->getMaxRoomNum($userRoom, $yersterday),
        ];

        return [
            'result' => [],
            'rows' => $rows,
            'success' => 1,
            'total' => 1,
            'charts' => [
                'onlineOpt' => $onlineOpt,
                'playOpt' => $playOpt,
            ]
        ];
    }

    public function liveMatchView(Request $request)
    {
        return view('Analysis/Player/liveMatchView', [
            'pageTitle' => $this->role->getCurrentPageTitle($request),
            'request' => $request
        ]);
    }

    public function liveMatchList(Request $request)
    {
        $date = $request->query->get('created');
        $date = \DateTime::createFromFormat('m/d/Y', $date)->format('Y-m-d');
        $userHall = new UserHall();
        $userRoomExt = new UserRoomExt();
        $node = new Node();
        $xAxis = $userHall->genTimes();
        $defaultOpt = [
            'tooltip' => ['trigger' => 'axis'],
            'xAxis' => ['data' => $xAxis],
            'yAxis' => ["type" => 'value'],
            'series' => [],
        ];
        $gameOpt = $defaultOpt;
        $userHall->getUserRoomData($userRoomExt, $date);
        $onlineGamesCfg = config('gm.game_alias');
        $games = [];
        foreach ($onlineGamesCfg as $k => $v) {
            $games[$k] = $v['name'];
        }

        $gameOpt['legend']['data'] = array_values($games);
        $selected = [];
        foreach ($onlineGamesCfg as $k => $v) {
            // if (!$v['select']) {
            $selected[$v['name']] = true;
            // }
        }
        $gameOpt['legend']['selected'] = $selected;

        $series = [];
        foreach ($games as $k => $v) {
            $temp = [
                'name' => $v,
                'type' => 'line',
                'data' => $userHall->getGameData($node, $k, $date),
            ];

            $series[] = $temp;
        }
        $gameOpt['series'] = $series;
        $rows = $userHall->getRealtimeGame($onlineGamesCfg, $node);

        return [
            'result' => [],
            'rows' => $rows,
            'success' => 1,
            'total' => 1,
            'charts' => [
                'gameOpt' => $gameOpt,
            ]
        ];
    }

    public function onlineView(Request $request)
    {
        return view('Analysis/Player/onlineView', [
            'pageTitle' => $this->role->getCurrentPageTitle($request),
            'request' => $request
        ]);
    }

    public function onlineList(Request $request)
    {
        $limit = $request->query->get('limit', 20);
        $offset = $request->query->get('offset', 0);
        $sort = $request->query->get('sort', 'id');
        $order = $request->query->get('order', 'desc');
        $clientId = $request->query->get('client_id');
        $roomName = $request->query->get('room_name');
        $uid = $request->query->get('uid');


        $model = new AccountsToday();
        !empty($sort) && $model = $model->orderBy($sort, $order);
        $uid && $model = $model->where('accounts_today.uid', $uid);
        $clientId && $model = $model->where('accounts_ext.client_id', $clientId);
        $roomName && $model = $model->where('accounts_today.room_name', $roomName);

        $model = $model->select(
            'accounts_today.id',
            'accounts_today.uid',
            'accounts_today.roomid',
            'accounts_today.room_name',
            'accounts_today.gameid',
            'accounts_today.all_result',
            'accounts_today.update_time',
            'accounts_today.nodeid',
            'accounts_today.today_result',
            'accounts_today.history_result',
            'account_ext.client_id'
        );
        $model->leftjoin('account_ext', 'account_ext.uid', '=', 'accounts_today.uid');
        $model->where('accounts_today.update_time', '=', date('Y-m-d'));
        // $model->where('accounts_today.roomid', '>', 0);
        $total = $model->count();
        $rows = $model->offset($offset)->limit($limit)->get()->toArray();
        return [
            'result' => [],
            'rows' => $rows,
            'success' => 1,
            'total' => $total,
        ];
    }

    public function roomWinLoseView(Request $request)
    {
        return view('Analysis/Player/roomWinLoseView', [
            'pageTitle' => $this->role->getCurrentPageTitle($request),
            'request' => $request
        ]);
    }

    public function roomWinLoseList(Request $request)
    {
        $limit = (int) $request->query->get('limit', 20);
        $offset = (int) $request->query->get('offset', 0);
        $sort = $request->query->get('sort', 'game_id');
        $order = $request->query->get('order', 'desc');
        $gameId = (int) $request->query->get('game_id');
        $created = $request->query->get('created');
        $created = urldecode($created);
        $created = explode(' - ', $created);
        $s  = \DateTime::createFromFormat('m/d/Y', $created[0])->format('Y-m-d');
        $e  = \DateTime::createFromFormat('m/d/Y', $created[1])->format('Y-m-d');

        $st  = $s . ' 00:00:00';
        $et  = $e . ' 23:59:59';

        $sortMaps = [
            'game_id' => 'a.game_id',
            'sum_gold' => 'a.sum_gold',
            'sum_tax' => 'b.sum_tax',
            'sum_history_gold' => 'b.sum_history_gold',
            'sum_history_tax' => 'b.sum_history_tax'
        ];

        $orderMaps = [
            'desc' => 'desc',
            'asc' => 'asc',
        ];

        $order = $orderMaps[$order] ?? '';
        $sort = $sortMaps[$sort] ?? '';

        // $sql = "
        // select a.game_id, a.sum_gold, b.sum_history_gold, a.sum_tax, b.sum_history_tax, c.RTP, c.valid_user,
        // c.bet_amount, c.payout_amount
        // from
        // (select game_id, sum(gold) as sum_gold, sum(tax) as sum_tax from room_gold_day_statistics 
        // where create_date = '{$e}' group by game_id) a
        // left join 
        // (select game_id, sum(gold) as sum_history_gold, sum(tax) as sum_history_tax from room_gold_day_statistics 
        // where create_date >= '{$s}' and create_date <= '{$e}' group by game_id) b
        // on a.game_id = b.game_id
        // left join 
        // (select game_id, (sum(bet_amount + transfer_amount) / sum(bet_amount)) * 10000 as RTP, 
        // sum(bet_amount) as bet_amount,
        // sum(bet_amount + transfer_amount) as payout_amount,
        // count(distinct(player_uid)) as valid_user from transfer_inout 
        // where create_time >= '{$st}' and create_time <= '{$et}' group by game_id) c
        // on a.game_id = c.game_id
        // where 1=1
        // ";
        $sql = "
        select a.game_id, a.sum_gold, b.sum_history_gold, a.sum_tax, b.sum_history_tax, c.RTP, c.RTPET, c.valid_user,
        c.bet_amount, c.payout_amount
        from
        (select game_id, sum(gold) as sum_gold, sum(tax) as sum_tax from room_gold_day_statistics 
        where create_date = '{$e}' group by game_id) a
        left join 
        (select game_id, sum(gold) as sum_history_gold, sum(tax) as sum_history_tax from room_gold_day_statistics 
        where create_date >= '{$s}' and create_date <= '{$e}' group by game_id) b
        on a.game_id = b.game_id
        left join 
        (select game_id, 
        (sum(bet_amount + transfer_amount) / sum(bet_amount)) * 10000 as RTP, 
        (sum(bet_amount + transfer_amount - tax) / sum(bet_amount)) * 10000 as RTPET,
        sum(bet_amount) as bet_amount,
        sum(bet_amount + transfer_amount) as payout_amount,
        sum(valid_user_cnt) as valid_user from data_report 
        where count_date >= '{$s}' and count_date <= '{$e}' group by game_id) c
        on a.game_id = c.game_id
        where 1=1
        ";
        $countSql = "select count(*) as cnt
        from
        (select game_id, sum(gold) as sum_gold from room_gold_day_statistics 
        where create_date = '{$e}' group by game_id) a
        left join 
        (select game_id, sum(gold) as sum_history_gold from room_gold_day_statistics 
        where create_date >= '{$s}' and create_date <= '{$e}' group by game_id) b
        on a.game_id = b.game_id
        where 1=1";
        if (!empty($gameId)) {
            $sql .= " and a.game_id = {$gameId}";
            $countSql .= " and a.game_id = {$gameId}";
        }

        if (!empty($order)) {
            $sql .= " order by {$sort} {$order}";
        }
        $sql .= " LIMIT {$offset}, {$limit}";
        $data = DB::connection('Master')->select($sql);
        $count = DB::connection('Master')->select($countSql);

        return [
            'result' => [],
            'rows' => $data,
            'success' => 1,
            'total' => current($count)->cnt,
            // 'sql' => $sql,
        ];
    }

}
