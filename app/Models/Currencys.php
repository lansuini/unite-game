<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Currencys extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'name',
        'exchange_rate',
        'count_month',
        'created',
        'updated'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
    ];

    protected $table = 'currencys';

    protected $connection = 'Master';

    
    public $timestamps = false;
}
