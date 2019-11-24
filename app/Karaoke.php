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
      return  $this->hasOne(Comment::class,'id','karaoke_id');
    }

    public function district(){
      return  $this->hasMany(District::class,'id','district_id');
    }

}
