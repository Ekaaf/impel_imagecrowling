<?php

namespace App\Http\Controllers;


use File; 
use Hash;
use App\User;
use App\Group;
use App\Venue;
use App\Examinee;
use Redirect;
use Response;
use App\Helpers\Conversion;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\SSP;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Exception;
use Illuminate\Support\Facades\Cache;

class ImageController extends Controller
{
    public function index(){
        return view('index');
    }

    public function matchimage(Request $request){
        libxml_use_internal_errors(true);
        $imageurl = uniqid().'.jpg';
        $destinationPath = "public/img/";
        $request->file('imagefile')->move($destinationPath,$imageurl);
        $imageList = [];

        $urls = $request->submiturl;
        foreach ($urls as $url) {
            $imageList = $this->exactImage($request, $url, $destinationPath,$imageurl);
        }
        return view('imagelist')->with('matchedImages',$imageList['matchedImages'])->with('possibleMatches',$imageList['possibleMatches']);
    }

    public function exactImage($request, $url, $destinationPath,$imageurl){

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 120,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET"
        ));
        $html = curl_exec($curl);
        // $html = file_get_contents($url);
        $dom = new \domDocument;
        $dom->loadHTML($html);
        $dom->preserveWhiteSpace = false;
        $images = $dom->getElementsByTagName('img');
        $imageList['matchedImages'] = [];
        $imageList['possibleMatches'] = [];
        $searchtags = $request->searchtags;
        $i = 0; $j = 0;
        if($images && count($images)>0){
            foreach ($images as $image) {
                $src = $image->getAttribute('src');
                if($src){
                    $alt = $image->getAttribute('alt');
                    $fullPath = pathinfo($src);
                    $fileName = $fullPath['filename'];
                    if (filter_var($src, FILTER_VALIDATE_URL)) {
                        $serverImagePath = $fullPath['dirname'].'/'.rawurlencode($fullPath['basename']);
                    } 
                    else if(substr($src,0,2)==''){
                        $serverImagePath = $src;
                    }
                    else{
                        $parse = parse_url($url);
                        $serverImagePath = $parse['scheme'].'://'.$parse['host'].'/'.$fullPath['dirname'].'/'.rawurlencode($fullPath['basename']);
                    }
                    
                    $md5image1 = md5(file_get_contents($destinationPath.$imageurl));
                    $md5image2 = md5(file_get_contents($serverImagePath));
                    if($md5image1==$md5image2){
                        $imageList['matchedImages'][$i]['website'] = $url;
                        $imageList['matchedImages'][$i]['images'] = $serverImagePath;
                        $imageList['matchedImages'][$i]['src'] = $src;
                        $i++;
                    }
                    foreach ($searchtags as $tag) {
                        if($tag!='' && ($tag == $alt || $tag == $fileName)){
                            $imageList['possibleMatches'][$j]['website'] = $url;
                            $imageList['possibleMatches'][$j]['images'] = $serverImagePath;
                            $imageList['possibleMatches'][$j]['src'] = $src;
                            $j++;
                        }
                    }
                }
                
            }
        }
        return $imageList;
    }
}
