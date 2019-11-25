<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Karaoke;
use App\Comment;
use App\District;
use App\City;
use DB;
use Illuminate\Support\Facades\Redis;
class KaraokeController extends Controller
{
    public function crawlSave(Request $request){
        //convert json to array
        $karaoke =  $request->json()->all();
        $city =  City::where('name',$karaoke['City'])->first();
        if($city == Null){
            $city = new City();
            $city->name = $karaoke['City'];
            $city->save();
        }
            //Create and save karaoke
        $district =  District::where('name',$karaoke['District'])->first();
        if($district == null){
            $district = new District();
            $district->id = $karaoke['DistrictId'];
            $district->name = $karaoke['District'];
            $district->city_id = $city->id;
            $district->save();
        }
            $data = new Karaoke();
            $data->name = $karaoke['Name'];
            $data->id = $karaoke['Id'];
            $data->avatar = $karaoke['MobilePicturePath'];
            $data->district_id = $district->id;
            $data->address = $karaoke['Address'];
            $data->phone = $karaoke['Phone'];
            $data->detail_url = $karaoke['DetailUrl'];
            //check tồn tại time
            if(isset($karaoke['TimeOpen'])){
                $data->time_open = $karaoke['TimeOpen']; 
            }else{
                    $data->time_open = null;
            }
            if(is_numeric($karaoke['AvgRating'])){
                $data->rating = $karaoke['AvgRating'];
            }else{
                $data->rating = null;
            }
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
                    $comment[] = array(
                        'Comment'=>$key['Comment'],
                        'CreatedOn'=>$key['CreatedOn'],
                        'Rating'=>$key['AvgRating'],
                        'FullName'=>$key['OwnerFullName'],
                        'Avatar'=>$key['OwnerAvatar'],
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
        $data =  District::join('karaokes','districts.id','=','karaokes.district_id')
                            ->join('cities','districts.city_id','=','cities.id')
                            ->select('karaokes.*','districts.name as district','cities.name as city')->oldest()->paginate(10);
        return response()->json(['Message'=>$data],200); 
    }

    public function show($id){
        $data =  District::join('karaokes','districts.id','=','karaokes.district_id')
        ->join('cities','districts.city_id','=','cities.id')->where('karaokes.id','=',$id)
        ->select('karaokes.*','districts.name as district','cities.name as city')->first();  
        if($data){  
            $review = Comment::where('karaoke_id',$data->id)->first();
            $data['reviews'] = json_decode($review['comment']);
            return response()->json(['Message'=>$data],200);
        }
        return response()->json(['Message'=>'Karaoke do not exits ! '],404);
    }

    public function destroy($id){
        $data = Karaoke::findOrFail($id);
        $review = Comment::where('karaoke_id',$id)->first();
        if($review){
            $review->delete();
        }
        $data->delete();
        return response()->json(['Message'=>'delete success'],200);
    }

    public function destroyAll(){
        Karaoke::truncate();
        Comment::truncate();
        return response()->json(['Message'=>'delete success'],200);
    }

    public function rating(){
    $city = "Hà Nội";
    $data =  District::join('karaokes','districts.id','=','karaokes.district_id')
    ->join('cities','districts.city_id','=','cities.id')->where('cities.name','=',$city)
    ->select('karaokes.*','districts.name as district','cities.name as city')->orderBy('karaokes.rating','desc')->take(10)->get();
       return response()->json(['Message'=>$data],200);
    }

    public function getAll(){
        $data = District::join('karaokes','districts.id','=','karaokes.district_id')
        ->join('cities','districts.city_id','=','cities.id')
        ->select('karaokes.*','districts.name as district','cities.name as city')->get();
	return response()->json(['Message'=>$data],200); 
    }

    public function listProvinces(){
       $provinces = [
        "name"=> "Hà Nội",
        "cities" => [[  "01" =>"Quận Ba Đình"],
        [ "02" =>"Quận Hoàn Kiếm"],
        [ "03" =>"Quận Tây Hồ"],
         ["04" =>"Quận Long Biên"], 
         ["05" =>"Quận Cầu Giấy"],
         [ "06" =>"Quận Đống Đa"], 
         [ "07" =>"Quận Hai Bà Trưng"], 
         [ "08" =>"Quận Hoàng Mai"], 
         [ "09" =>"Quận Thanh Xuân"], 
         [ "010" =>"Quận Nam Từ Liêm"], 
         [ "011" =>"Quận Bắc Từ Liêm"], 
         [ "012" =>"Huyện Sóc Sơn"], 
         [ "013" =>"Huyện Đông Anh"], 
         [ "014" =>"Huyện Gia Lâm"], 
         [ "015" =>"Huyện Thanh Trì"], 
         [ "016" =>"Huyện Mê Linh"], 
         [ "017" =>"Quận Hà Đông"], 
         [ "018" =>"Thị xã Sơn Tây"], 
         [ "019" =>"Huyện Ba Vì"], 
         [ "020" =>"Huyện Phúc Thọ"], 
         [ "021" =>"Huyện Đan Phượng"], 
         [ "022" =>"Huyện Hoài Đức"], 
         [ "023" =>"Huyện Quốc Oai"], 
         [ "024" =>"Huyện Thạch Thất"], 
         [ "025" =>"Huyện Chương Mỹ"], 
         [ "026" =>"Huyện Thanh Oai"], 
         [ "027" =>"Huyện Thường Tín"], 
         [ "028" =>"Huyện Phú Xuyên"], 
         [ "029" =>"Huyện Ứng Hòa"], 
         [ "030" =>"Huyện Mỹ Đức" ],]
       ];
        return response()->json(['Message'=>$provinces],200); 
    }

	public function testAll(){

	if($data = json_decode(Redis::get('test.All'))){
		 return response()->json(['Message'=>$data],200); 
	}
 	    $data = District::join('karaokes','districts.id','=','karaokes.district_id')
        	->join('cities','districts.city_id','=','cities.id')
       		->select('karaokes.*','districts.name as district','cities.name as city')->get();
        	Redis::setex('test.All',60*60*24,json_encode($data));
		return response()->json(['Message'=>$data],200); 
      
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
