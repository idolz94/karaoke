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
            //Create and save karaoke
             $data = new Karaoke();
            $data->name = $karaoke['Name'];
            $data->id = $karaoke['Id'];
            $data->avatar = $karaoke['MobilePicturePath'];
            $data->city = $karaoke['City'];
            $data->district = $karaoke['District'];
            $data->address = $karaoke['Address'];
            $data->phone = $karaoke['Phone']; 
            if(isset($karaoke['Price'])){
            $data->price = $karaoke['Price']; 
            }else{
                $data->price = null;
            }
            if(isset($karaoke['TimeOpen'])){
                $data->time_open = $karaoke['TimeOpen']; 
            }else{
                    $data->time_open = null;
            }
            $data->rating = $karaoke['AvgRating'];
            $data->ltn = $karaoke['Latitude'];
            $data->lgn = $karaoke['Longitude'];
            if(isset($karaoke['Video']) && sizeof($karaoke['Video']) >0){
                foreach ($karaoke['Video'] as $key) {
                    $video[] = array(
                      'videos'=>$key
                    );
                }
                $videos = json_encode($video,true);
                $data->video = $videos;
            }else{
                $data->video = null;
            }
            if(isset($karaoke['Album']) && sizeof($karaoke['Album']) >0){
                foreach ($karaoke['Album'] as $key) {
                    $album[] = array(
                      'images'=>$key
                    );
                }
                $albums = json_encode($album,true);
                $data->album = $albums;
            }else{
                $data->album = null;
            }
           
            $data->save();
            if(sizeof($karaoke['Reviews']) >0){
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
                }
                $commentJson = json_encode($comment,true);
                $comments = new Comment();
                $comments->karaoke_id = $data->id;
                $comments->comment = $commentJson;
                $comments->save();
            }
         return response()->json(['Message'=>'success'],200);
    }

    public function show($id){
        $data = Karaoke::find($id);
       if($data){
       $review =$data->comments()->first();
        $data['reviews'] = json_decode($review['comment']);
        return response()->json(['Message'=>$data],200);
        }
        return response()->json(['Message'=>'Karaoke do not exits'],404);
    }

    public function destroy($id){
        $data = Karaoke::find($id);
        if($data){
            $data->delete();
            return response()->json(['Message'=>'delete success'],200);
        }
        return response()->json(['Message'=>'Karaoke do not exits'],404);
    }
}
