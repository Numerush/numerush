<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Laravel\Passport\Client;

use App\User;
use App\Models\Gambar;
use App\Models\VerifyUser;

use App\Mail\VerifyMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

use Auth;
use Hash;

class RegisterController extends Controller
{
    private $client;

    public function __construct() {
        $this->client = Client::find(1);
    }

    public function register(Request $request) {
        $this->validate($request, [
            'uid' => 'required',
            'name' => 'required',
            'telepon' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed'
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'uid' => request('uid'),
                'name' => request('name'),
                'email' => request('email'),
                'telepon' => request('telepon'),
                'password' => bcrypt(request('password')),
                'rincian' => "Kosong",
                'verified' => 1
            ]);

            $verifyUser = VerifyUser::create([
                'user_id' => $user->id,
                'token' => sha1(time())
            ]);

            // Mail::to($user->email)->send(new VerifyMail($user));
        } catch(Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Terjadi kesalahan pada sistem kami: ' . $e->getMessage()]);
        }
        DB::commit();

        return response()->json(['message' => 'Kami telah mengirimkan link verifikasi ke email ' . $user->email . '. Silahkan cek email Anda']);
    }

    public function verifyUser($token) {
        $verifyUser = VerifyUser::where('token', $token)->first();
        if(isset($verifyUser)) {
            $user = $verifyUser->user;
            if(!$user->verified) {
                $verifyUser->user->verified = 1;
                $verifyUser->user->save();
                $status = "Your e-mail is verified. You can now login.";
            }else{
                $status = "Your e-mail is already verified. You can now login.";
            }
        } else {
            return response()->json(['warning'=> "Sorry your email cannot be identified."]);
        }
 
        return response()->json(['message' => $status]);
    }

    public function changePassword(Request $request){
        if (!(Hash::check($request->get('current_password'), Auth::user()->password))) {
            // The passwords matches
            return  response()->json(["message"=>"Kata sandi lama anda tidak sesuai. Silahkan coba lagi."]);
        }
        if(strcmp($request->get('current_password'), $request->get('new_password')) == 0){
            //Current password and new password are same
            return  response()->json(["message"=>"Kata sandi baru tidak dapat sama dengan kata sandi lama. Coba kata sandi lainnya."]);
        }
        $validatedData = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);
        //Change Password
        $user = Auth::user();
        $user->password = bcrypt($request->get('new_password'));
        $user->save();
        return response()->json(['message' => 'Sukses mengganti kata sandi']);
    }

    public function editUser(Request $request)
    {
        $user = Auth::user();

        if(isset($request->name))
            $user->name = $request->name;
        if(isset($request->email))
            $user->email = $request->email;
        if(isset($request->telepon))
            $user->telepon = $request->telepon;
        if(isset($request->rincian))
            $user->rincian = $request->rincian;
        
        if(isset($request->avatar))
        {
            $path = Gambar::savePictureToServer($request->avatar);
            $user->avatar = $path;
        }

        $user->save();
        return response()->json(['message' => 'Sukses mengganti data user']);
    }
}
