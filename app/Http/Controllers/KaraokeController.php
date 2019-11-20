<?php

namespace App\Http\Controllers;
use DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Karaoke;
use App\Comment;
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
            $data->album = $karaoke['AlbumUrl'];
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
        $data = Karaoke::oldest()->paginate(10);
        return response()->json(['Message'=>$data],200); 
    }

    public function show($id){
        $data = Karaoke::findOrFail($id);
        $review =$data->comments()->first();
        $data['reviews'] = json_decode($review['comment']);
        return response()->json(['Message'=>$data],200);
       // return response()->json(['Message'=>'Karaoke do not exits'],404);
    }

    public function destroy($id){
        $data = Karaoke::findOrFail($id);
        $data->delete();
        return response()->json(['Message'=>'delete success'],200);
      //  return response()->json(['Message'=>'Karaoke do not exits'],404);
    }

    public function destroyAll(){
        Karaoke::truncate();
        Comment::truncate();
        return response()->json(['Message'=>'delete success'],200);
    }

    public function rating(){
        $data = Karaoke::whereCity('Hà Nội')->orderBy(DB::raw('ABS(rating)'),'DESC')->take(10)->get();
        return response()->json(['Message'=>$data],200);
    }

    public function getAll(){
        $data = Karaoke::all();
        return response()->json(['Message'=>$data],200); 
    }
}
