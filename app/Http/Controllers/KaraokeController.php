<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Karaoke;
use App\Comment;
class KaraokeController extends Controller
{
    public function crawlSave(Request $request){
        $karaoke =  $request->json()->all();
      //  dd($karaoke);
            //Create and save karaoke
             $data = new Karaoke();
            $data->name = $karaoke['Name'];
            $data->avatar = $karaoke['MobilePicturePath'];
            $data->city = $karaoke['City'];
            $data->district = $karaoke['District'];
            $data->address = $karaoke['Address'];
            $data->phone = "";
            $data->price = "";
            $data->time_open = "";
            $data->rating = $karaoke['AvgRating'];
            $data->ltn = $karaoke['Latitude'];
            $data->lgn = $karaoke['Longitude'];
            $data->album = [];
            $data->video = [];
            $data->save();
            foreach ($karaoke['Reviews'] as $key) {
                $comment[] = array(
                    'name'=>$key['OwnerFullName'],
                    'title'=>$key['Title'],
                    'avatar'=>$key['OwnerAvatar'],
                    'comment'=>$key['Comment'],
                    'pictures'=>[],
                    'rating'=>$key['AvgRating'],
                    'created_on'=>$key['CreatedOn'],
                );
          
            $commentJson = json_encode($comment);
            $comments = new Comment();
            $comments->karaoke_id = $data->id;
            $comments->comment = $commentJson;
            $comments->save();
        }
         return response()->json(['Message'=>'success'],200);
    }
}
