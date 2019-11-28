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

        // check có tồn tại city
        $city = City::where('name','=',$karaoke['City'])->first();
        if($city == Null){
            $city = new City();
            $city->name = $karaoke['City'];
            $city->url = str_slug($karaoke['City'],'-');
            $city->save();
        }
            //check có tồn tại tỉnh, thành
        $district =  District::where('name','=',$karaoke['District'])->first();
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

    public function indexCity($city){
    
        $data =  District::join('karaokes','districts.id','=','karaokes.district_id')
        ->join('cities','districts.city_id','=','cities.id')->where('cities.url','=',$city)
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

    public function rating($city){
    $data =  District::join('karaokes','districts.id','=','karaokes.district_id')
    ->join('cities','districts.city_id','=','cities.id')->where('cities.url','=',$city)
    ->select('karaokes.*','districts.name as district','cities.name as city')->orderBy('karaokes.rating','desc')->take(10)->get();
        return response()->json(['Message'=>$data],200);
    }

    public function getAll($city){
        $data = District::join('karaokes','districts.id','=','karaokes.district_id')
        ->join('cities','districts.city_id','=','cities.id')->where('cities.url','=',$city)
        ->select('karaokes.*','districts.name as district','cities.name as city')->get();
	return response()->json(['Message'=>$data],200); 
    }

    public function listProvinces(){
       $jsonProvinces = file_get_contents(public_path('provinces.json'));
       $provinces = json_decode($jsonProvinces, true);
        return response()->json(['Message'=>$provinces],200); 
    }

	public function testAll(){
        if($data = json_decode(Redis::get('test.All'))){
            return response()->json(['Message'=>$data],200); 
	}
 	    $data = District::join('karaokes','districts.id','=','karaokes.district_id')
        	->join('cities','districts.city_id','=','cities.id')
       		->select('karaokes.*','districts.name as district','cities.name as city')->get();
        	Redis::setex('test.All',60*60,json_encode($data));
		return response()->json(['Message'=>$data],200); 
      
    }
    // public function get(){
    //     $citiesDB = City::all();
    //     $city = DB::table('devvn_tinhthanhpho')->get();
    //     $quanhuyen = DB::table('devvn_quanhuyen')->get();
    //     $district = District::all();
    //     foreach ($city as $cities) {
    //         if($cities->name == "Tỉnh Bà Rịa - Vũng Tàu"){
    //             foreach ($quanhuyen as $value) {
    //                     if($cities->matp == $value->matp){
    //                         $name = trim(str_replace("Huyện",'',$value->name));
    //                         foreach ($district as $key) {
                        
    //                             if($key->name == $name){
                              
    //                                 $get[$key->id] = $value->name;
    //                             }else{
    //                             $a[] = $value->name;
    //                             }
    //                         }
    //                     }   
    //                 }
    //             $diff = array_unique(array_diff($a,$get));
    //         $merge = array_merge($get,$diff);
    //         foreach ($diff as $key) {
    //             array_push($get,$key);
    //         }
    //         $cities = trim(str_replace("Tỉnh Bà Rịa - ",'',$cities->name));
    //         foreach ($citiesDB as $cityDB) {   
    //             if($cityDB->name == $cities){
    //                 foreach ($get as $key => $value) {
    //                         $all['name'] = $cityDB->name;
    //                         $all['url'] = $cityDB->url;
    //                         $all['cities'][] = [$key => $value];
    //                 }
    //             }
    //         }
    //         return response()->json(['Message'=>$all],200); 
    //         }
    //     }
    // }
}
