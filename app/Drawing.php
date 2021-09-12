<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Drawing extends Model
{
    use SoftDeletes;
    protected $table = 'drawings';
    protected $fillable = [
        'hull_id',
        'type',
        'drawing_no',
        'drawing_title',
        'pdf',
        'dwf',
        'dwg',
        'revision_no',
        'date_published',
        'uploaded_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
