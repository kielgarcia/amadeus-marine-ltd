<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RevisionHistory extends Model
{
    protected $table = 'revision_histories';
    protected $fillable = ['drawing_id','revision_no','uploaded_by','date_published','pdf','dwf','dwg'];
    public $timestamps = false;
}
