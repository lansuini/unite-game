<?php

namespace App\Models\Manager\Merchant;

use App\Models\Manager\Admin as Model;

class Admin extends Model
{
    protected $table = 'merchant_admin';

    protected $connection = 'Master';

    public $tag = 'MERC_';

    protected $fillable = [
        'id',
        'username',
        'password',
        'nickname',
        'is_bind_google_code',
        'google_captcha',
        'last_login_ip',
        'last_login_time',
        'last_update_password_time',
        'last_bind_google_code_time',
        'err_login_cnt',
        'is_lock',
        'role_id',
        'ip_white',
        'created',
        'client_id'
    ];

    public function getCurrent($request)
    {
        if ($request->ip() == '127.0.0.1' && $request->get('_export') == 1) {
            return self::where('id', $request->get('_admin_id'))->first();
        }
        return self::where('id', $this->getLoginID($request))->first();
    }
}
