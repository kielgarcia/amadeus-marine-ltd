<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Zdatalog extends Model
{
    protected $table = 'zdatalog';
	protected $fillable = ['action','primary_id','user_id','created_at'];
    public $timestamps = false;
}
