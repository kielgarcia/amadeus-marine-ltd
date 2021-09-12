<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    //
    protected $table = 'permissions';
    protected $fillable = ['name','description','guard_name','created_at','updated_at'];
}
