<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ConfigAttribute extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'v_key_name',
        't_key_value',
        'v_type',
        'v_desc',
        'i_create_date',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
    ];

    protected $table = 'config_attribute';

    protected $connection = 'Master';

    
    public $timestamps = false;
}
