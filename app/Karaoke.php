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

    public function comments(){
      return  $this->hasOne(Comment::class);
    }

    protected $casts = [
        'album' => 'json',
        'video' => 'json',
      ];
}
