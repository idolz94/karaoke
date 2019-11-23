<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $fillable = [
        'name', 'ma_tp'
    ];
    public function city(){
        return  $this->hasMany(City::class,'ma_tp');
    }
      public function karaoke(){
        return  $this->belongsTo(Karaoke::class);
      }
}
