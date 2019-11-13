<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Karaoke extends Model
{
    protected $fillable = [
        'name', 'avatar', 'city','district','address',
        'phone','price','time_open','rating','ltn',
        'lgn','album','video',
    ];

    public function comment(){
        $this->hasOne(Comment::class,'karaoke_id');
    }

    protected $casts = [
        'album' => 'json',
        'video' => 'json',
      ];
}
