<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Auth;
use File;

class Gambar extends Model
{
    protected $fillable = ['path_gambar', 'detail_produk_id'];

    public function detail_produk()
    {
        return $this->belongsTo(DetailProduk::class);
    }
    
    public static function savePictureToServer($gambarURL){
        // try
        // {
        //     $contents = file_get_contents($gambarURL);
        // }
        // catch(\ErrorException $e)
        // {
        //     return response()->json(array("error"=> 'Gambar kosong'));
        // }
        
        // $path      = parse_url($gambarURL, PHP_URL_PATH);       // get path from url
        // $extension = pathinfo($path, PATHINFO_EXTENSION);
        // $extension = ($extension == 'tmp') ? 'jpg' : $extension;
        // $extension = 'webp';

        // $path = 'img_user/' . Auth::user()->id . '/';
        // if (!file_exists($path)) {
        //     File::makeDirectory($path,0755,true);
        // }

        // $nama =  $filename = str_random(10) . '_'. date("Ymdhis") . '.webp';
        // $gambarPath = $path . $filename;
        // $save = file_put_contents($gambarPath, $contents);

        return $gambarURL;
    }

    public static function uploadAvatar($gambarURL){
        // try
        // {
        //     $contents = file_get_contents($gambarURL);
        // }
        // catch(\ErrorException $e)
        // {
        //     return response()->json(array("error"=> 'Gambar kosong'));
        // }
        
        // $path      = parse_url($gambarURL, PHP_URL_PATH);       // get path from url
        // $extension = pathinfo($path, PATHINFO_EXTENSION);
        // $extension = ($extension == 'tmp') ? 'jpg' : $extension;
        // $extension = 'webp';

        // $path = 'avatar/' . Auth::user()->id . '/';
        // if (!file_exists($path)) {
        //     File::makeDirectory($path,0755,true);
        // }

        // $nama =  $filename = str_random(10) . '_'. date("Ymdhis") . '.webp';
        // $gambarPath = $path . $filename;
        // $save = file_put_contents($gambarPath, $contents);

        return $gambarURL;
    }
}
