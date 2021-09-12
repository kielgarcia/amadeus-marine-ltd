<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hull extends Model
{
    use SoftDeletes;
    protected $table = 'hulls';
    protected $fillable = [
        'hull_no',
        'hull_description',
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
