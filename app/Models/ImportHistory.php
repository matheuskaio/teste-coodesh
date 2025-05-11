<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportHistory extends Model
{

    protected $fillable = [
        'filename',
        'imported_count',
        'imported_at',
    ];

    protected $dates = ['imported_at'];
}
