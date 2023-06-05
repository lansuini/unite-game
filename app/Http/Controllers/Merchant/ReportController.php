<?php

namespace App\Http\Controllers\Merchant;

use Illuminate\Http\Request;
use App\Models\Manager\Role;

use App\Models\DataReport;
use App\Models\DataReportSub;
use App\Models\CustomerSub;
use App\Models\UserEnterOut;

class ReportController extends MerchantController
{
    public function totalView(Request $request)
    {
        return view('Merchant/Report/totalView', [
            'pageTitle' => $this->role->getCurrentPageTitle($request),
            'role' => new Role, 'request' => $request
        ]);
    }

    public function totalList(Request $request)
    {
        $limit = $request->query->get('limit', 20);
        $offset = $request->query->get('offset', 0);
        $sort = $request->query->get('sort', 'id');
        $order = $request->query->get('order', 'desc');
        $gameID = $request->query->get('gameid');

        $created = $request->query->get('created');
        $created = urldecode($created);
        $created = explode(' - ', $created);

        $s  = \DateTime::createFromFormat('m/d/Y', $created[0])->format('Y-m-d');
        $e  = \DateTime::createFromFormat('m/d/Y', $created[1])->format('Y-m-d');

        $clientId = $this->admin->getCurrent($request)->client_id;

        $model = new UserEnterOut();
        !empty($sort) && $model = $model->orderBy($sort, $order);
        $gameID && $model = $model->where('node.gameid', $gameID);
        $model = $model->select(
            'node.gameid',
            $model->raw('sum(change_gold) as win_lose')
        );
        $model = $model->leftjoin('account', 'account.uid', '=', 'user_enter_out.uid');
        $model = $model->leftjoin('node', 'node.id', '=', 'user_enter_out.nodeid');
        $model = $model->where('user_enter_out.post_time', '>=', $s);
        $model = $model->where('user_enter_out.post_time', '<=', $e);
        $model = $model->where('account.client_id', $clientId);
        $model = $model->where('user_enter_out.client_id', $clientId);
        $model = $model->where('user_enter_out.type', 3);
        $model = $model->where('user_enter_out.result', '!=', 0);
        $model = $model->groupBy('node.gameid');
        $total = $model->count();
        $rows = $model->offset($offset)->limit($limit)->get()->toArray();
        return [
            'result' => [],
            'rows' => $rows,
            'success' => 1,
            'total' => $total,
        ];
    }

    public function dayView(Request $request)
    {
        return view('Merchant/Report/dayView', [
            'pageTitle' => $this->role->getCurrentPageTitle($request),
            'role' => new Role, 'request' => $request
        ]);
    }

    public function dayList(Request $request)
    {
        $limit = $request->query->get('limit', 20);
        $offset = $request->query->get('offset', 0);
        $sort = $request->query->get('sort', 'id');
        $order = $request->query->get('order', 'desc');
        $gameID = $request->query->get('gameid');

        $created = $request->query->get('created');
        $created = urldecode($created);
        $created = explode(' - ', $created);

        $s  = \DateTime::createFromFormat('m/d/Y', $created[0])->format('Y-m-d');
        $e  = \DateTime::createFromFormat('m/d/Y', $created[1])->format('Y-m-d');

        $clientId = $this->admin->getCurrent($request)->client_id;

        $model = new UserEnterOut();
        !empty($sort) && $model = $model->orderBy($sort, $order);
        $gameID && $model = $model->where('node.gameid', $gameID);
        $model = $model->select(
            'node.gameid',
            'user_enter_out.post_time',
            $model->raw('sum(change_gold) as win_lose')
        );
        $model = $model->leftjoin('account', 'account.uid', '=', 'user_enter_out.uid');
        $model = $model->leftjoin('node', 'node.id', '=', 'user_enter_out.nodeid');
        $model = $model->where('user_enter_out.post_time', '>=', $s);
        $model = $model->where('user_enter_out.post_time', '<=', $e);
        $model = $model->where('account.client_id', $clientId);
        $model = $model->where('user_enter_out.client_id', $clientId);
        $model = $model->where('user_enter_out.type', 3);
        $model = $model->where('user_enter_out.result', '!=', 0);
        $model = $model->groupBy('node.gameid', 'user_enter_out.post_time');
        $total = $model->count();
        $rows = $model->offset($offset)->limit($limit)->get()->toArray();
        return [
            'result' => [],
            'rows' => $rows,
            'success' => 1,
            'total' => $total,
        ];
    }

    public function dataReportView(Request $request)
    {
        return view('Merchant/Report/dataReportView', [
            'pageTitle' => $this->role->getCurrentPageTitle($request),
            'request' => $request
        ]);
    }

    public function dataReportList(Request $request)
    {
        $isExport = (int) $request->query->get('_export', 0);
        $limit = (int) $request->query->get('limit', 20);
        $offset = (int) $request->query->get('offset', 0);
        $sort = $request->query->get('sort', 'game_id');
        $order = $request->query->get('order', 'desc');
        $gameId = (int) $request->query->get('game_id');
        // $clientId = (int) $request->query->get('client_id');
        $created = $request->query->get('created');
        $created = urldecode($created);
        $created = explode(' - ', $created);
        $s  = \DateTime::createFromFormat('m/d/Y', $created[0])->format('Y-m-d');
        $e  = \DateTime::createFromFormat('m/d/Y', $created[1])->format('Y-m-d');


        $model = new DataReport();
        !empty($sort) && $model = $model->orderBy($sort, $order);
        $clientId = $this->admin->getCurrent($request)->client_id;
        $model = $model->where('data_report.client_id', $clientId);
        $gameId && $model = $model->where('data_report.game_id', $gameId);
        $model = $model->whereBetween('data_report.count_date', [$s, $e]);
        $model = $model->select(
            'data_report.id',
            'data_report.game_id',
            'data_report.count_date',
            'data_report.updated_time',
            'data_report.transfer_amount',
            'data_report.bet_count',
            'data_report.bet_amount',
            'data_report.client_id',
            'data_report.tax',
            'data_report.valid_user_cnt',
            'data_report.login_user_cnt',
            $model->raw('((transfer_amount + bet_amount) / bet_amount)  * 10000 as RTP'),
            $model->raw('((transfer_amount + bet_amount - tax) / bet_amount)  * 10000 as RTPET'),
        );

        $total = $model->count();
        $rows = $model->offset($offset)->limit($limit)->get()->toArray();
        if ($isExport != 1 || ($isExport == 1 && $offset + $limit >= $total)) {
            $model = new DataReport();
            $clientId && $model = $model->where('data_report.client_id', $clientId);
            $gameId && $model = $model->where('data_report.game_id', $gameId);
            $model = $model->whereBetween('data_report.count_date', [$s, $e]);
            $model = $model->select(
                $model->raw('sum(bet_count) as bet_count'),
                $model->raw('sum(bet_amount) as bet_amount'),
                $model->raw('sum(transfer_amount) as transfer_amount'),
                $model->raw('sum(tax) as tax'),
                $model->raw('sum(valid_user_cnt) as valid_user_cnt'),
                $model->raw('sum(login_user_cnt) as login_user_cnt'),
                $model->raw('(sum(bet_amount + transfer_amount) / sum(bet_amount)) * 10000 as RTP'),
                $model->raw('(sum(bet_amount + transfer_amount - tax) / sum(bet_amount)) * 10000 as RTPET')
            );
            $row = $model->first();
            $rows[] = [
                'game_id' => 'TOTAL',
                'bet_amount' => $row->bet_amount,
                'bet_count' => $row->bet_count,
                'transfer_amount' => $row->transfer_amount,
                'tax' => (int) $row->tax,
                'valid_user_cnt' => (int) $row->valid_user_cnt,
                'login_user_cnt' => (int) $row->login_user_cnt,
                'RTP' => (int) $row->RTP,
                'RTPET' => (int) $row->RTPET,
            ];
        }
        return [
            'result' => [],
            'rows' => $rows,
            'success' => 1,
            'total' => $total,
        ];
    }

    public function subDataReportView(Request $request)
    {
        // $clientId = $this->admin->getCurrent($request)->client_id;
        // $customerSubType = CustomerSub::where('customer_id', $clientId)->pluck('symbol', 'id')->toArray();
        // $res = [];
        // foreach ($customerSubType as $k => $v) {
        //     $res[] = ['key' => $k, 'value' => $v];
        // }
        return view('Merchant/Report/subDataReportView', [
            'pageTitle' => $this->role->getCurrentPageTitle($request),
            'request' => $request,
            // 'customerSubType' => $res,
        ]);
    }

    public function subDataReportList(Request $request)
    {
        $isExport = (int) $request->query->get('_export', 0);
        $clientId = $this->admin->getCurrent($request)->client_id;
        $limit = (int) $request->query->get('limit', 20);
        $offset = (int) $request->query->get('offset', 0);
        $sort = $request->query->get('sort', 'game_id');
        $order = $request->query->get('order', 'desc');
        $gameId = (int) $request->query->get('game_id');
        $clientIdSub = (int) $request->query->get('client_id_sub');
        $created = $request->query->get('created');
        $created = urldecode($created);
        $created = explode(' - ', $created);
        $s  = \DateTime::createFromFormat('m/d/Y', $created[0])->format('Y-m-d');
        $e  = \DateTime::createFromFormat('m/d/Y', $created[1])->format('Y-m-d');

        $model = new DataReportSub();
        !empty($sort) && $model = $model->orderBy($sort, $order);
        $model = $model->where('client_id', $clientId);
        $clientIdSub && $model = $model->where('client_id_sub', $clientIdSub);
        $gameId && $model = $model->where('game_id', $gameId);
        $model = $model->whereBetween('count_date', [$s, $e]);
        $model = $model->select(
            'id',
            'game_id',
            'count_date',
            'updated_time',
            'transfer_amount',
            'bet_count',
            'bet_amount',
            'client_id_sub',
            'tax',
            'valid_user_cnt',
            'login_user_cnt',
            $model->raw('((transfer_amount + bet_amount) / bet_amount)  * 10000 as RTP'),
            $model->raw('((transfer_amount + bet_amount - tax) / bet_amount)  * 10000 as RTPET'),
        );

        $total = $model->count();
        $rows = $model->offset($offset)->limit($limit)->get()->toArray();

        if ($isExport != 1 || ($isExport == 1 && $offset + $limit >= $total)) {
            $model = new DataReportSub();
            $clientId && $model = $model->where('client_id', $clientId);
            $clientIdSub && $model = $model->where('client_id_sub', $clientIdSub);
            $gameId && $model = $model->where('game_id', $gameId);
            $model = $model->whereBetween('count_date', [$s, $e]);
            $model = $model->select(
                $model->raw('sum(bet_count) as bet_count'),
                $model->raw('sum(bet_amount) as bet_amount'),
                $model->raw('sum(transfer_amount) as transfer_amount'),
                $model->raw('sum(tax) as tax'),
                $model->raw('sum(valid_user_cnt) as valid_user_cnt'),
                $model->raw('sum(login_user_cnt) as login_user_cnt'),
                $model->raw('(sum(bet_amount + transfer_amount) / sum(bet_amount)) * 10000 as RTP'),
                $model->raw('(sum(bet_amount + transfer_amount - tax) / sum(bet_amount)) * 10000 as RTPET')
            );
            $row = $model->first();
            $rows[] = [
                'client_id_sub' => 'TOTAL',
                'bet_amount' => (int) $row->bet_amount,
                'bet_count' => (int) $row->bet_count,
                'transfer_amount' => (int) $row->transfer_amount,
                'tax' => (int) $row->tax,
                'valid_user_cnt' => (int) $row->valid_user_cnt,
                'login_user_cnt' => (int) $row->login_user_cnt,
                'RTP' => (int) $row->RTP,
                'RTPET' => (int) $row->RTPET,
            ];
        }
        return [
            'result' => [],
            'rows' => $rows,
            'success' => 1,
            'total' => $total,
        ];
    }
}
