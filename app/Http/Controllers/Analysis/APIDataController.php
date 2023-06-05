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
use App\Models\TransferInOut;
use App\Models\GameDetails;
use App\Models\DataReport;
use App\Models\DataReportSub;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class APIDataController extends AnalysisController
{
    public function transferInOutView(Request $request)
    {
        return view('Analysis/APIData/transferInOutView', [
            'pageTitle' => $this->role->getCurrentPageTitle($request),
            'request' => $request
        ]);
    }

    public function transferInOutList(Request $request)
    {
        $limit = (int) $request->query->get('limit', 20);
        $offset = (int) $request->query->get('offset', 0);
        $sort = $request->query->get('sort', 'game_id');
        $order = $request->query->get('order', 'desc');
        $gameId = (int) $request->query->get('game_id');
        $clientId = (int) $request->query->get('client_id');
        $clientIdSub = (int) $request->query->get('client_id_sub');
        $billType = (int) $request->query->get('bill_type');
        $uid = $request->query->get('uid');
        $playerName = $request->query->get('player_name');
        $transactionId = $request->query->get('transaction_id');
        $parentBetId = $request->query->get('parent_bet_id');
        $status = $request->query->get('status', -1);
        $created = $request->query->get('created');
        $s  = \DateTime::createFromFormat('m/d/Y', $created)->format('Y-m-d 00:00:00');
        $e  = \DateTime::createFromFormat('m/d/Y', $created)->format('Y-m-d 23:59:59');

        $model = new TransferInOut();
        if ($model->checkClientTable($clientId)) {
            $transferInOutTableName = 'transfer_inout_' . $clientId;
            $model = $model->setTable($transferInOutTableName);
            !empty($sort) && $model = $model->orderBy($sort, $order);
            $clientIdSub && $model = $model->where($transferInOutTableName . '.client_id', $clientIdSub);
            $gameId && $model = $model->where($transferInOutTableName . '.game_id', $gameId);
            $uid && $model = $model->where($transferInOutTableName . '.player_uid', $uid);
            $billType && $model = $model->where($transferInOutTableName . '.bill_type', $billType);
            $transactionId && $model = $model->where($transferInOutTableName . '.transaction_id', $transactionId);
            $parentBetId && $model = $model->where($transferInOutTableName . '.parent_bet_id', $parentBetId);
            $playerName && $model = $model->where($transferInOutTableName . '.player_name', $playerName);
            $status != -1 && $model = $model->where($transferInOutTableName . '.status', $status);

            $model = $model->whereBetween($transferInOutTableName . '.create_time', [$s . ' 00:00:00', $e . ' 23:59:59']);
            $model = $model->select(
                $transferInOutTableName . '.id',
                $transferInOutTableName . '.player_uid',
                'account.player_name',
                // $transferInOutTableName . '.token',
                $transferInOutTableName . '.parent_bet_id',
                $transferInOutTableName . '.bet_id',
                $transferInOutTableName . '.bet_amount',
                $transferInOutTableName . '.transfer_amount',
                $transferInOutTableName . '.transaction_id',
                $transferInOutTableName . '.bill_type',
                $transferInOutTableName . '.is_end',
                $transferInOutTableName . '.create_time',
                $transferInOutTableName . '.game_id',
                $transferInOutTableName . '.status',
                $transferInOutTableName . '.client_id as client_id_sub',
                $transferInOutTableName . '.balanceBefore',
                $transferInOutTableName . '.balanceAfter',
            );

            $model = $model->leftjoin('account', 'account.uid', '=', $transferInOutTableName . '.player_uid');
            $total = $model->count();
            $rows = $model->offset($offset)->limit($limit)->get()->toArray();
        } else {
            $rows = [];
            $total = 0;
        }
        return [
            'result' => [],
            'rows' => $rows,
            'success' => 1,
            'total' => $total,
        ];
    }

    public function gameDetailsView(Request $request)
    {
        return view('Analysis/APIData/gameDetailsView', [
            'pageTitle' => $this->role->getCurrentPageTitle($request),
            'request' => $request
        ]);
    }

    public function gameDetailsList(Request $request)
    {
        $limit = (int) $request->query->get('limit', 20);
        $offset = (int) $request->query->get('offset', 0);
        $sort = $request->query->get('sort', 'game_id');
        $order = $request->query->get('order', 'desc');
        $gameId = (int) $request->query->get('game_id');
        $clientId = (int) $request->query->get('client_id');
        $clientIdSub = (int) $request->query->get('client_id_sub');
        $uid = $request->query->get('uid');
        $playerName = $request->query->get('player_name');
        $created = $request->query->get('created');
        $s  = \DateTime::createFromFormat('m/d/Y', $created)->format('Y-m-d 00:00:00');
        $e  = \DateTime::createFromFormat('m/d/Y', $created)->format('Y-m-d 23:59:59');

        $model = new GameDetails();
        if ($model->checkClientTable($clientId)) {
            $gameDetailsTableName = 'game_details_' . $clientId;
            $model = $model->setTable($gameDetailsTableName);
            !empty($sort) && $model = $model->orderBy($sort, $order);
            $clientIdSub && $model = $model->where('client_id', $clientIdSub);
            $gameId && $model = $model->where('game_id', $gameId);
            $uid && $model = $model->where('uid', $uid);
            $playerName && $model = $model->where('player_name', $playerName);
            $model = $model->whereBetween('create_time', [$s . ' 00:00:00', $e . ' 23:59:59']);
            $model = $model->select(
                'id',
                'uid',
                'player_name',
                'parent_bet_id',
                'bet_id',
                'create_time',
                'game_id',
                'detail',
                'client_id as client_id_sub'
            );

            $total = $model->count();
            $rows = $model->offset($offset)->limit($limit)->get()->toArray();
        } else {
            $rows = [];
            $total = 0;
        }
        return [
            'result' => [],
            'rows' => $rows,
            'success' => 1,
            'total' => $total,
        ];
    }

    public function dataReportView(Request $request)
    {
        return view('Analysis/APIData/dataReportView', [
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
        $clientId = (int) $request->query->get('client_id');
        $created = $request->query->get('created');
        $created = urldecode($created);
        $created = explode(' - ', $created);
        $s  = \DateTime::createFromFormat('m/d/Y', $created[0])->format('Y-m-d');
        $e  = \DateTime::createFromFormat('m/d/Y', $created[1])->format('Y-m-d');

        $model = new DataReport();
        !empty($sort) && $model = $model->orderBy($sort, $order);
        $clientId && $model = $model->where('client_id', $clientId);
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
            'client_id',
            'tax',
            'valid_user_cnt',
            'login_user_cnt',
            $model->raw('((transfer_amount + bet_amount) / bet_amount)  * 10000 as RTP'),
            $model->raw('((transfer_amount + bet_amount - tax) / bet_amount)  * 10000 as RTPET'),
        );

        $total = $model->count();
        $rows = $model->offset($offset)->limit($limit)->get()->toArray();

        if ($isExport != 1 || ($isExport == 1 && $offset + $limit >= $total)) {
            $model = new DataReport();
            $clientId && $model = $model->where('client_id', $clientId);
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
                'client_id' => 'TOTAL',
                'bet_amount' => (int) $row->bet_amount,
                'bet_count' => (int) $row->bet_count,
                'transfer_amount' => (int)$row->transfer_amount,
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
        return view('Analysis/APIData/subDataReportView', [
            'pageTitle' => $this->role->getCurrentPageTitle($request),
            'request' => $request
        ]);
    }

    public function subDataReportList(Request $request)
    {
        $isExport = (int) $request->query->get('_export', 0);
        $limit = (int) $request->query->get('limit', 20);
        $offset = (int) $request->query->get('offset', 0);
        $sort = $request->query->get('sort', 'game_id');
        $order = $request->query->get('order', 'desc');
        $gameId = (int) $request->query->get('game_id');
        $clientId = (int) $request->query->get('client_id');
        $clientIdSub = (int) $request->query->get('client_id_sub');
        $created = $request->query->get('created');
        $created = urldecode($created);
        $created = explode(' - ', $created);
        $s  = \DateTime::createFromFormat('m/d/Y', $created[0])->format('Y-m-d');
        $e  = \DateTime::createFromFormat('m/d/Y', $created[1])->format('Y-m-d');

        $model = new DataReportSub();
        !empty($sort) && $model = $model->orderBy($sort, $order);
        $clientId && $model = $model->where('client_id', $clientId);
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
            'client_id',
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
                'client_id' => 'TOTAL',
                'client_id_sub' => '-',
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
