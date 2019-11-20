<?php

namespace App\Http\Controllers;
use DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Karaoke;
use App\Comment;
use DB;
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
<<<<<<< HEAD
        $data = Karaoke::whereCity('Hà Nội')->orderBy(DB::raw('ABS(rating)'),'desc')->take(10)->get();
=======
        $data = Karaoke::whereCity('Hà Nội')->orderBy(DB::raw('ABS(rating)'),'DESC')->take(10)->get();
>>>>>>> 592a78fbf77b9d8f87c9d55e945011537826e502
        return response()->json(['Message'=>$data],200);
    }

    public function getAll(){
        $data = Karaoke::all();
        return response()->json(['Message'=>$data],200); 
    }

    public function listProvinces(){
        
       $provinces = array(
        "name"=> "Hà Nội",
        "cities" => array(
           "01" =>"Quận Ba Đình", 
           "02" =>"Quận Hoàn Kiếm", 
           "03" =>"Quận Tây Hồ", 
           "04" =>"Quận Long Biên", 
           "05" =>"Quận Cầu Giấy", 
           "06" =>"Quận Đống Đa", 
           "07" =>"Quận Hai Bà Trưng", 
           "08" =>"Quận Hoàng Mai", 
           "09" =>"Quận Thanh Xuân", 
           "010" =>"Quận Nam Từ Liêm", 
           "011" =>"Quận Bắc Từ Liêm", 
           "012" =>"Huyện Sóc Sơn", 
           "013" =>"Huyện Đông Anh", 
           "014" =>"Huyện Gia Lâm", 
           "015" =>"Huyện Thanh Trì", 
           "016" =>"Huyện Mê Linh", 
           "017" =>"Quận Hà Đông", 
           "018" =>"Thị xã Sơn Tây", 
           "019" =>"Huyện Ba Vì", 
           "020" =>"Huyện Phúc Thọ", 
           "021" =>"Huyện Đan Phượng", 
           "022" =>"Huyện Hoài Đức", 
           "023" =>"Huyện Quốc Oai", 
           "024" =>"Huyện Thạch Thất", 
           "025" =>"Huyện Chương Mỹ", 
           "026" =>"Huyện Thanh Oai", 
           "027" =>"Huyện Thường Tín", 
           "028" =>"Huyện Phú Xuyên", 
           "029" =>"Huyện Ứng Hòa", 
           "030" =>"Huyện Mỹ Đức", 
            )
        );
        return response()->json(['Message'=>$provinces],200); 
    }

    // public function distance($lng,$lng1,$lat,$lat1){
    //     $theta =  $lng - $lng1;
    //     $miles = (sin(deg2rad($lat)) * sin(deg2rad($lat1))) + (cos(deg2rad($lat)) * cos(deg2rad($lat1)) * cos(deg2rad($theta)));
    //     $miles = acos($miles);
    //     $miles = rad2deg($miles);
    //     $miles = $miles * 60 * 1.1515;
    //     $feet = $miles * 5280;
    //     $yards = $feet / 3;
    //     $kilometers = $miles * 1.609344;
    //     return $meters = $kilometers * 1000;
    // }
    // public function map(Request $request){
    //     $data = $request->json()->all();
    //     $lat = $data['Latitude'];
    //     $lng = $data['Longitude'];
    //     $karaoke = Karaoke::all();
    //     foreach ($karaoke as $key) {
    //      $key['distance'] = $this->distance($lng,$key['lgn'],$lat,$key['ltn']);
    //     }
    //     $kara = $karaoke->sortBy('distance');
    //     return response()->json(['Message'=>$kara],200); 
    // }
}
