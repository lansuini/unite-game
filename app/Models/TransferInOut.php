<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TransferInOut extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'player_uid',
        'token',
        'parent_bet_id',
        'bet_id',
        'bet_amount',
        'transfer_amount',
        'transaction_id',
        'bill_type',
        'is_end',
        'create_time',
        'game_id',
        'status',
        'client_id',
        'balanceBefore',
        'balanceAfter',
        'api_mode',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [];

    protected $table = 'transfer_inout';

    protected $connection = 'Master';

    public $timestamps = false;

    public function retryCreateAllClientTable()
    {
        $c = Customer::get();
        foreach ($c as $v) {
            $this->createTable($v->id);
        }
    }

    public function st($num)
    {
        return $this->setTable("transfer_inout_{$num}");
    }

    public function checkClientTable($num)
    {
        return Schema::connection($this->connection)->hasTable("transfer_inout_{$num}");
    }

    public function createTable($num)
    {
        if (!Schema::connection($this->connection)->hasTable("transfer_inout_{$num}")) {
            $createTableSqlString = "
            CREATE TABLE `transfer_inout_{$num}` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
                `player_uid` bigint(20) DEFAULT NULL COMMENT '用户ID',
                `token` varchar(128) DEFAULT NULL COMMENT '用户TOKEN',
                `parent_bet_id` varchar(64) DEFAULT NULL COMMENT '母单号',
                `bet_id` varchar(64) DEFAULT NULL COMMENT '子单号',
                `bet_amount` int(11) DEFAULT NULL COMMENT '下注数目',
                `transfer_amount` int(11) DEFAULT NULL COMMENT '输赢',
                `transaction_id` varchar(128) DEFAULT NULL COMMENT '标识ID',
                `bill_type` int(11) DEFAULT NULL COMMENT '流水类型',
                `is_end` tinyint(4) DEFAULT NULL COMMENT '是否结束',
                `create_time` datetime DEFAULT NULL COMMENT '创建时间',
                `game_id` int(11) DEFAULT NULL COMMENT '游戏ID',
                `status` tinyint(1) DEFAULT '0' COMMENT '状态',
                `client_id` int(11) DEFAULT '0' COMMENT '渠道ID',
                `balanceBefore` int(11) DEFAULT NULL COMMENT '玩家交易前余额',
                `balanceAfter` int(11) DEFAULT NULL COMMENT '玩家交易后余额',
                `api_mode` tinyint(4) DEFAULT NULL COMMENT '钱包模式',
                UNIQUE KEY `idx_transaction_id` (`transaction_id`),
                KEY `id` (`id`),
                KEY `idx_client_id_create_time` (`client_id`,`create_time`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
            DB::connection($this->connection)->statement($createTableSqlString);
        }

        if (!Schema::connection($this->connection)->hasTable("transfer_inout_backup_{$num}")) {
            $createTableSqlString = "
            CREATE TABLE `transfer_inout_backup_{$num}` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
                `player_uid` bigint(20) DEFAULT NULL COMMENT '用户ID',
                `token` varchar(128) DEFAULT NULL COMMENT '用户TOKEN',
                `parent_bet_id` varchar(64) DEFAULT NULL COMMENT '母单号',
                `bet_id` varchar(64) DEFAULT NULL COMMENT '子单号',
                `bet_amount` int(11) DEFAULT NULL COMMENT '下注数目',
                `transfer_amount` int(11) DEFAULT NULL COMMENT '输赢',
                `transaction_id` varchar(128) DEFAULT NULL COMMENT '标识ID',
                `bill_type` int(11) DEFAULT NULL COMMENT '流水类型',
                `is_end` tinyint(4) DEFAULT NULL COMMENT '是否结束',
                `create_time` datetime DEFAULT NULL COMMENT '创建时间',
                `game_id` int(11) DEFAULT NULL COMMENT '游戏ID',
                `status` tinyint(1) DEFAULT '0' COMMENT '状态',
                `client_id` int(11) unsigned DEFAULT '0' COMMENT '渠道ID',
                `balanceBefore` int(11) DEFAULT NULL COMMENT '玩家交易前余额',
                `balanceAfter` int(11) DEFAULT NULL COMMENT '玩家交易后余额',
                `api_mode` tinyint(4) DEFAULT NULL COMMENT '钱包模式',
                UNIQUE KEY `idx_transaction_id` (`transaction_id`),
                KEY `id` (`id`),
                KEY `idx_create_time` (`create_time`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
            DB::connection($this->connection)->statement($createTableSqlString);
        }

        if (!Schema::connection($this->connection)->hasTable("server_post_log_{$num}")) {
            $createTableSqlString = "
            CREATE TABLE `server_post_log_{$num}` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `trace_id` varchar(128) DEFAULT NULL,
                `uid` bigint(20) DEFAULT NULL,
                `client_id` int(11) unsigned DEFAULT '0',
                `type` tinyint(1) DEFAULT NULL,
                `arg` varchar(1024) DEFAULT NULL,
                `return` varchar(1024) DEFAULT NULL,
                `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `ip` varchar(128) DEFAULT NULL,
                `error_code` int(11) DEFAULT NULL,
                `error_text` varchar(250) DEFAULT NULL,
                `cost_time` int(11) DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_client_id_created` (`created`)
              ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
            DB::connection($this->connection)->statement($createTableSqlString);
        }

        if (!Schema::connection($this->connection)->hasTable("server_post_sub_log_{$num}")) {
            $createTableSqlString = "
            CREATE TABLE `server_post_sub_log_{$num}` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `pid` bigint(20) DEFAULT NULL,
                `transfer_reference` varchar(50) DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `idx_transfer_reference` (`transfer_reference`),
                KEY `idx_pid` (`pid`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
            DB::connection($this->connection)->statement($createTableSqlString);
        }

        if (!Schema::connection($this->connection)->hasTable("server_request_log_{$num}")) {
            $createTableSqlString = "
            CREATE TABLE `server_request_log_{$num}` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `pid` bigint(20) unsigned DEFAULT '0',
                `client_id` int(11) unsigned DEFAULT '0',
                `uid` bigint(20) DEFAULT NULL,
                `type` int(11) DEFAULT NULL,
                `url` varchar(512) DEFAULT NULL,
                `cost_time` int(11) DEFAULT NULL,
                `response` varchar(2048) DEFAULT NULL,
                `error_code` int(11) DEFAULT NULL,
                `params` varchar(2048) DEFAULT NULL,
                `method` varchar(20) DEFAULT NULL,
                `code` int(11) DEFAULT NULL,
                `args` varchar(2048) DEFAULT NULL,
                `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `is_success` tinyint(1) DEFAULT '0',
                `admin_id` int(11) DEFAULT '0',
                `error_text` varchar(250) DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `pid` (`pid`),
                KEY `idx_client_id_created` (`client_id`, `created`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
            DB::connection($this->connection)->statement($createTableSqlString);
        }

        if (!Schema::connection($this->connection)->hasTable("game_details_{$num}")) {
            $createTableSqlString = "
            CREATE TABLE `game_details_{$num}` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
                `parent_bet_id` varchar(64) DEFAULT NULL COMMENT '母单号',
                `bet_id` varchar(64) DEFAULT NULL COMMENT '子单号',
                `create_time` datetime DEFAULT NULL COMMENT '创建时间',
                `game_id` int(11) DEFAULT NULL COMMENT '游戏ID',
                `detail` varchar(256) DEFAULT NULL COMMENT '明细',
                `uid` int(11) DEFAULT NULL COMMENT '用户ID',
                `player_name` varchar(50) DEFAULT NULL COMMENT '第三方玩家标识',
                `client_id` int(10) unsigned DEFAULT '0',
                KEY `id` (`id`),
                KEY `idx_client_id_create_time` (`client_id`,`create_time`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
            DB::connection($this->connection)->statement($createTableSqlString);
        }

        if (!Schema::connection($this->connection)->hasTable("transfer_inout_server_request_log_{$num}")) {
            $createTableSqlString = "
            CREATE TABLE `transfer_inout_server_request_log_{$num}` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `transaction_id` varchar(128) DEFAULT NULL,
                `server_request_log_id` bigint(20) DEFAULT NULL,
                `queue_name` varchar(20) DEFAULT NULL,
                `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `idx_transaction_id_srl_id` (`transaction_id`,`server_request_log_id`),
                KEY `idx_created` (`created`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
            DB::connection($this->connection)->statement($createTableSqlString);
        }
    }
}
