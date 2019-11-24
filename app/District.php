<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $fillable = [
        'name', 'city_id'
    ];
    public function city(){
        return  $this->hasMany(City::class,'id','city_id');
    }
      public function karaoke(){
        return  $this->belongsTo(Karaoke::class);
      }
}
