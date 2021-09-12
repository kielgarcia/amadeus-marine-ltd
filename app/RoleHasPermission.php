<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoleHasPermission extends Model
{
    protected $table = 'role_has_permissions';
    protected $fillable = ['permission_id','role_id'];
    public $timestamps = false;
}
