<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Karaoke;
use App\Comment;
use DateTime;
class KaraokeController extends Controller
{
    public function crawlSave(Request $request){
        //convert json to array
        $karaoke =  $request->json()->all();
        //dd($karaoke);
            //Create and save karaoke
            $data = new Karaoke();
            $data->name = $karaoke['Name'];
            $data->id = $karaoke['Id'];
            $data->avatar = $karaoke['MobilePicturePath'];
            $data->city = $karaoke['City'];
            $data->district = $karaoke['District'];
            $data->address = $karaoke['Address'];
            $data->phone = $karaoke['Phone'];
            $data->detail_url = $karaoke['DetailUrl'];
            //check tồn tại time
            if(isset($karaoke['TimeOpen'])){
                $data->time_open = $karaoke['TimeOpen']; 
            }else{
                    $data->time_open = null;
            }
            $data->rating = $karaoke['AvgRating'];
            $data->ltn = $karaoke['Latitude'];
            $data->lgn = $karaoke['Longitude'];
            //check tồn tại và rỗng video 
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
              //check tồn tại và rỗng album 
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
           // check trùng id crawl
            if(Karaoke::find($data->id)){
                return response()->json(['Message'=>'id has been duplicated'],404);
            }else{
                $data->save();
            }
              //check rỗng review 
            if(sizeof($karaoke['Reviews']) >0){
                foreach ($karaoke['Reviews'] as $key) {
                    //check rỗng picture review
                    if($key['Pictures'] !== Null){
                        foreach ($key['Pictures'] as $item) {
                            $pictures[] = array($item['Url']);
                        }
                    }else{
                        $pictures = null;
                    }
                    $comment[] = array(
                        'name'=>$key['OwnerFullName'],
                        'title'=>$key['Title'],
                        'avatar'=>$key['OwnerAvatar'],
                        'comment'=>$key['Comment'],
                        'pictures'=>$pictures,
                        'rating'=>$key['AvgRating'],
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

    public function index(){
        $data = Karaoke::latest()->paginate(10);
        return response()->json(['Message'=>$data],200); 
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

    public function destroyAll(){
        Karaoke::truncate();
        Comment::truncate();
        return response()->json(['Message'=>'delete success'],200);
    }

    public function rating(){
        $data = Karaoke::where('city','Hà Nội')->orderBy('rating','Desc')->take(10)->get();
        return response()->json(['Message'=>$data],200);
    }
}
