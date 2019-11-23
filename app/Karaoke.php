<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Karaoke extends Model
{
    protected $fillable = [
        'name', 'avatar', 'district_id','address',
        'phone','price','time_open','rating','ltn',
        'lgn','album','video',
    ];

    public function comments(){
      return  $this->hasOne(Comment::class);
    }

    public function district(){
      return  $this->hasMany(District::class,'district_id');
    }

    protected $casts = [
        'album' => 'json',
        'video' => 'json',
      ];
}
