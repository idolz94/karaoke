<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'karaoke_id', 'comment'
    ];

    public function karaoke(){
      return  $this->belongsTo(Karaoke::class,'karaoke_id');
    }

    protected $casts = [
        'comment' => 'json',
      ];
}
