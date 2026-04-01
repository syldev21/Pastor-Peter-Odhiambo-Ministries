<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExportLog extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'metadata',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
