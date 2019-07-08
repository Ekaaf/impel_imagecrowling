<?php

namespace App\Http\Controllers;


use File; 
use Response;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Cache;
use Jenssegers\ImageHash\ImageHash;
use Jenssegers\ImageHash\Implementations\DifferenceHash;


class ImageController extends Controller
{
    public function index(){
        // $hasher = new ImageHash(new DifferenceHash());
        // $hash1 = $hasher->hash('http://www.praavahealth.com/public/images/praava%20logo+familydoctors.png');
        // $hash2 = $hasher->hash('http://www.praavahealth.com/public/images/praava%20logo+familydoctors.png');

        // $distance = $hasher->distance($hash1, $hash2);
        // dd($distance);
        return view('index');
    }

    public function matchimage(Request $request){
        libxml_use_internal_errors(true);
        $imageurl = uniqid().'.jpg';
        $destinationPath = "public/img/";
        $request->file('imagefile')->move($destinationPath,$imageurl);
        $imageList = [];
        $imageList['matchedImages'] = [];
        $imageList['possibleMatches'] = [];
        $urls = $request->submiturl;
        foreach ($urls as $url) {
            $data = $this->exactImage($request, $url, $destinationPath,$imageurl);
            if(isset($data['matchedImages'])){
                foreach ($data['matchedImages'] as $image) {
                    $imageList['matchedImages'][] = $image;
                }
            }
            if(isset($data['possibleMatches'])){
                foreach ($data['possibleMatches'] as $image) {
                    $imageList['possibleMatches'][] = $image;
                }
            }
            
        }
        return view('imagelist')->with('matchedImages',$imageList['matchedImages'])->with('possibleMatches',$imageList['possibleMatches']);
    }


    public function parseDocument($url){
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
        return $images;
    }



    public function exactImage($request, $url, $destinationPath,$imageurl){
        $images = $this->parseDocument($url);
        $imageList = [];
        $searchtags = $request->searchtags;
        $i = 0; $j = 0;
        if($images && count($images)>0){
            if($request->option==1){
                $md5image1 = md5(file_get_contents($destinationPath.$imageurl));
            }
            else{
                $hasher = new ImageHash(new DifferenceHash());
                // $hash1 = $hasher->hash($destinationPath.$imageurl);
            }
            
            foreach ($images as $image) {
                $src = $image->getAttribute('src');
                if($src){
                    $test[] = $src;
                    $alt = $image->getAttribute('alt');
                    $parse = parse_url($url);
                    $fullPath = pathinfo($src);
                    $fileName = $fullPath['filename'];
                    if(substr($src,0,4)=='http'){
                        $serverImagePath = $fullPath['dirname'].'/'.str_replace(' ', '%20', $fullPath['basename']);
                    } 
                    else if(substr($src,0,2)=='//'){
                        $serverImagePath = $parse['scheme'].':'.$src;
                        // dd($serverImagePath);
                    }
                    else{
                        $serverImagePath = $parse['scheme'].'://'.$parse['host'].'/'.$fullPath['dirname'].'/'.str_replace(' ', '%20', $fullPath['basename']);
                    }
                    
                    if($request->option==1){
                        $md5image2 = md5(file_get_contents($serverImagePath));
                        if($md5image1==$md5image2){
                            $imageList['matchedImages'][$i]['website'] = $url;
                            $imageList['matchedImages'][$i]['images'] = $serverImagePath;
                            $imageList['matchedImages'][$i]['src'] = $src;
                            $i++;
                        }
                    }
                    else{
                        $hash1 = $hasher->hash($destinationPath.$imageurl);
                        $hash2 = $hasher->hash($serverImagePath);
                        $distance = $hasher->distance($hash1, $hash2);
                        // dd($distance);
                        if($distance<=5){
                            $imageList['matchedImages'][$i]['website'] = $url;
                            $imageList['matchedImages'][$i]['images'] = $serverImagePath;
                            $imageList['matchedImages'][$i]['src'] = $src;
                            $i++;
                        }
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
                dd($test);
            }
        }
        if(isset($imageList['matchedImages']) || isset($imageList['possibleMatches'])){
            return $imageList;
        }
        
    }

}
