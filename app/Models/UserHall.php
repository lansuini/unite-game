<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class UserHall extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'tbus',
        'online_num',
        'op_time',
        'client_id',
        // 'node_id_junior',
        // 'node_id_midle',
        // 'node_id_senior',
        // 'node_id_super_junior',
        // 'node_id_super_midle',
        // 'node_id_super_senior',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [];

    protected $table = 'user_hall';

    protected $connection = 'Master';

    
    public $timestamps = false;

    public function genTimes($range = 60, $day = '')
    {
        $ret = array();
        $start = strtotime('20001010');
        $max = $start + 86400;
        $now = time();
        while (($start < $now) && ($start <= $max)) {
            $ret[] =  date('H:i', $start);
            $start += $range;
        }
        return $ret;
    }

    /**
     * 获取默认的数值
     * @param array $timeArr
     * @return array
     */
    public function defaultRet($timeArr, $val = null)
    {
        if (empty($timeArr)) return [];
        $ret = [];
        foreach ($timeArr as $k => $v) {
            $ret[$k] = $val;
        }
        return $ret;
    }

    public function getHallData($userHall, $date)
    {
        if (empty($date)) return [];
        $start = "{$date} 00:00:00";
        $end = "{$date} 23:59:59";
        // $sql = "select * from user_hall where op_time between '{$start}' and '{$end}' ";
        // $arr = oo::db('log_comm')->getAll($sql);
        // $model = new UserHall;
        $arr = $userHall->whereBetween('op_time', [$start, $end])->get()->toArray();

        $timeArr = $this->genTimes();
        $defaultVal = $date == date('Y-m-d') ? null : 0;
        $ret = $this->defaultRet($timeArr, $defaultVal);
        $tempArray = [];

        //按进程-时间 封装数据
        foreach ($arr as $k => $v) {
            $time = substr($v['op_time'], 11, 5);
            if (isset($tempArray[$v['tbus']][$time])) {
                $tempArray[$v['tbus']][$time] = $tempArray[$v['tbus']][$time] > $v['online_num'] ? $tempArray[$v['tbus']][$time] : $v['online_num'];
            } else {
                $tempArray[$v['tbus']][$time] = (int)$v['online_num'];
            }
            unset($arr[$k]);
        }
        unset($arr);

        //封装时间节点数据
        foreach ($tempArray as $tbusId => $tbusData) {
            foreach ($tbusData as $time => $num) {
                $idx = array_search($time, $timeArr);

                //是当天，当前时间以后的分钟不显示
                if (false && ($date == date("Y-m-d")) && $this->compareCurrentTime($time)) {
                    $ret[$idx] = null;
                    unset($tempArray[$tbusId][$time]);
                    continue;
                }

                $ret[$idx] += (int)$num;
                unset($tempArray[$tbusId][$time]);
            }
        }
        unset($tempArray);

        return $ret;
    }

    public function getRoomData($userRoom, $date)
    {
        if (empty($date)) return [];
        $start = "{$date} 00:00:00";
        $end = "{$date} 23:59:59";
        // $sql = "select * from user_room where op_time between '{$start}' and '{$end}' ";
        // $arr = oo::db('log_comm')->getAll($sql);
        $arr = $userRoom->whereBetween('op_time', [$start, $end])->get()->toArray();
        // var_dump($arr);exit;
        $timeArr = $this->genTimes();
        $defaultVal = $date == date('Y-m-d') ? null : 0;
        $ret = $this->defaultRet($timeArr, $defaultVal);
        $tempArray = [];

        //按游戏-进程-时间 封装数据
        foreach ($arr as $k => $v) {
            $time = substr($v['op_time'], 11, 5);
            if (isset($tempArray[$v['game_id']][$v['tbus']][$time])) {
                $tempArray[$v['game_id']][$v['tbus']][$time] = $tempArray[$v['game_id']][$v['tbus']][$time] > $v['online_num'] ? $tempArray[$v['game_id']][$v['tbus']][$time] : $v['online_num'];
            } else {
                $tempArray[$v['game_id']][$v['tbus']][$time] = (int)$v['online_num'];
            }
            unset($arr[$k]);
        }
        unset($arr);

        //封装时间节点数据
        foreach ($tempArray as $gameId => $gameData) {
            foreach ($gameData as $tbusId => $tbusData) {
                foreach ($tbusData as $time => $num) {
                    $idx = array_search($time, $timeArr);

                    //是当天，当前时间以后的分钟不显示
                    if (false &&  ($date == date("Y-m-d")) && $this->compareCurrentTime($time)) {
                        $ret[$idx] = null;
                        unset($tempArray[$tbusId][$time]);
                        continue;
                    }

                    $ret[$idx] += (int)$num;
                    unset($tempArray[$gameId][$tbusId][$time]);
                }
            }
        }
        unset($tempArray);

        return $ret;
    }

    protected $roomData = [];

    /**
     * 获取实时牌局数据
     * @return array
     */
    public function getUserRoomData($userRoomExt, $date = null)
    {
        $date or $date = date('Y-m-d');
        $sdate = "{$date} 00:00:00";
        $edate = "{$date} 23:59:59";
        // $sql = "select tbus, node_id,game_id,online_num,op_time from user_room_ext where op_time between '{$sdate}' and '{$edate}'";
        // $arr = oo::db('log_comm')->getAll($sql);
        // $model = new UserRoomExt;
        $arr = $userRoomExt->select('tbus', 'node_id', 'game_id', 'online_num', 'op_time')->whereBetween('op_time', [$sdate, $edate])->get()->toArray();
        $ret = [];
        if ($arr) {
            foreach ($arr as $k => $v) {
                $hour = substr($v['op_time'], 11, 2);
                $m1 = substr($v['op_time'], 14, 1);
                $m2 = substr($v['op_time'], 15, 1);

                $time = sprintf("%s:%s%s", $hour, $m1, $m2);

                $nodeId = (int)$v['node_id'];
                $gameId = (int)$v['game_id'];
                $tbus = (int)$v['tbus'];

                // 同一个游戏，同一个节点，有可能有不同进程ID tbus 数据
                if (isset($ret[$gameId][$nodeId][$tbus][$time])) {
                    $ret[$gameId][$nodeId][$tbus][$time] = $ret[$gameId][$nodeId][$tbus][$time] > $v['online_num'] ? $ret[$gameId][$nodeId][$tbus][$time] : $v['online_num'];
                } else {
                    $ret[$gameId][$nodeId][$tbus][$time] = (int)$v['online_num'];
                }

                //循环中逐一销毁已经处理的数据，释放内存
                unset($arr[$k]);
            }
            unset($arr);
        }

        $res = [];
        foreach ($ret as $gameId => $gameData) {
            foreach ($gameData as $nodeId => $nodeData) {
                foreach ($nodeData as $tbusId => $tbusData) {
                    foreach ($tbusData as $time => $num) {
                        if (isset($res[$gameId][$nodeId][$time])) {
                            $res[$gameId][$nodeId][$time] += (int)$num;
                        } else {
                            $res[$gameId][$nodeId][$time] = (int)$num;
                        }
                        unset($ret[$gameId][$nodeId][$tbusId][$time]);
                    }
                }
            }
        }
        unset($ret);

        $this->roomData = $res;
        return $res;
    }

    public function getMaxHallNum($userHall, $date)
    {
        if (empty($date)) return 0;
        $start = "{$date} 00:00:00";
        $end = "{$date} 23:59:59";
        // $sql = "select max(online_num) as num from user_hall where op_time between '{$start}' and '{$end}' ";
        // $row = oo::db('log_comm')->getRow($sql);
        $row = $userHall->whereBetween('op_time', [$start, $end])->orderBy('online_num', 'desc')->first();
        return !empty($row) ? (int) $row->online_num : 0;
    }

    public function getMaxRoomNum($userRoom, $date)
    {
        if (empty($date)) return 0;
        $start = "{$date} 00:00:00";
        $end = "{$date} 23:59:59";
        // $sql = "select online_num,op_time,game_id,tbus from user_room where op_time between '{$start}' and '{$end}' ";
        // $arr = oo::db('log_comm')->getAll($sql);
        $arr = $userRoom->select('online_num', 'op_time', 'game_id', 'tbus')->whereBetween('op_time', [$start, $end])->get()->toArray();
        $num = 0;
        $ret = [];
        $tampArray = [];
        if ($arr) {
            foreach ($arr as $k => $v) {
                $time = substr($v['op_time'], 11, 5);
                $tampKey = $v['game_id'] . '_' . $v['tbus'] . '_' . $time;
                if (!isset($tampArray[$tampKey])) {
                    $tampArray[$tampKey] = $v['online_num'];
                    if (isset($ret[$time])) {
                        $ret[$time] += (int)$v['online_num'];
                    } else {
                        $ret[$time] = (int)$v['online_num'];
                    }
                }
            }
            $num = max($ret);
        }
        return $num;
    }

    /**
     * 获取房间在玩数据
     * @param int $game
     * @return array
     */
    public function getGameData($node, $gameId, $date = null)
    {
        $date or $date = date('Y-m-d');
        $nodeIds = $node->where('gameid', $gameId)->pluck('id')->toArray();

        if (empty($nodeIds)) return [];
        $now = time();

        $times = $this->genTimes();

        $ret = [];
        foreach ($times as $time) {
            if (strtotime("{$date} {$time}:00") + 60 > $now) {
                $ret[] = null;
                continue;
            }
            $num = 0;

            foreach ($nodeIds as $nodeId) {
                $num += isset($this->roomData[$gameId])
                    && isset($this->roomData[$gameId][$nodeId])
                    && isset($this->roomData[$gameId][$nodeId][$time])
                    ? $this->roomData[$gameId][$nodeId][$time] : 0;
            }
            $ret[] = $num;
        }
        return $ret;
    }

    /**
     * 获取实时的游戏信息
     * @return array
     */
    public function getRealtimeGame($onlineGamesCfg, $node)
    {
        $nodes = $node->select('id', 'name', 'gameid')->get()->toArray();
        $nodesName = [];
        $gameNodeArr = []; //game_id的node_id数组
        foreach ($nodes as $value) {
            $nodesName[$value['id']] = $value['name'];
            $gameNodeArr[$value['gameid']][] = (int)$value['id'];
        }

        $ret = [];
        $total = 0;

        $time = date("H:i", time() - 60);

        foreach ($onlineGamesCfg as $k => $v) {

            $gameId = $k;
            $nodeIds = isset($gameNodeArr[$gameId]) ? $gameNodeArr[$gameId] : [];

            $num = 0;


            foreach ($nodeIds as $nodeId) {
                $n = isset($this->roomData[$gameId])
                    && isset($this->roomData[$gameId][$nodeId])
                    && isset($this->roomData[$gameId][$nodeId][$time])
                    ? $this->roomData[$gameId][$nodeId][$time] : 0;
                $ret[] = [
                    'name' => $nodesName[$nodeId],
                    'num' => $n,
                ];
                $num += $n;
            }



            $total += $num;
        }
        $ret[] = ['name' => 'TOTAL', 'num' => $total];
        return $ret;
    }

    protected function compareCurrentTime($time) {
        list($h, $m) = explode(':', $time);
        $num1 = $h * 3600 + $m * 60;
        $num2 = date('h') * 3600 + date('i') * 60;
        return $num1 >= $num2;
    }
}
