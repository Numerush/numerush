<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Laravel\Passport\Client;
use Illuminate\Support\Facades\Route;

use DB;
use Socialite;
use App\User;
use App\Models\VerifyUser;

class SocialLoginController extends Controller
{
    private $client;

    public function __construct() {
        $this->client = Client::find(1);
    }

    public function redirectToProvider()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleProviderCallback()
    {
        $user = Socialite::driver('facebook')->user();

        $name = $user->name;
        $email = $user->email;
        $userid = $user->id;

        DB::beginTransaction();
        try {
            $user = User::create([
                'uid' => $userid,
                'name' => $name,
                'email' => $email,
                'password' => bcrypt($userid),
                'rincian' => "Kosong",
                'verified' => 1
            ]);

            $verifyUser = VerifyUser::create([
                'user_id' => $user->id,
                'token' => sha1(time())
            ]);

            $params = [
                'grant_type' => 'password',
                'client_id' => $this->client->id,
                'client_secret' => $this->client->secret,
                'username' => $email,
                'password' => $userid,
                'scope' => '*'
            ];
    
            $request->request->add($params);
            $proxy = Request::create('oauth/token', 'POST');
    
            return Route::dispatch($proxy);

            // Mail::to($user->email)->send(new VerifyMail($user));
        } catch(Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Login Facebook gagal karena terjadi kesalahan pada sistem kami: ' . $e->getMessage()]);
        }
        DB::commit();
    }

    public function googleAuth(Request $request)
    {
        $request->validate([
            'uid' => 'required',
            'id_token' => 'required',
        ]);
        
        $id_token = $request->id_token;

        $client = new \Google_Client(['client_id' => env("GOOGLE_CLIENT_ID")]);  // Specify the CLIENT_ID of the app that accesses the backend
        $payload = $client->verifyIdToken($id_token);
        if ($payload) {
            $userid = $payload['sub'];
            $email = $payload['email'];
            $name = $payload['name'];
            
            DB::beginTransaction();
            try {
                $user = User::create([
                    'uid' => request('uid'),
                    'name' => $name,
                    'email' => $email,
                    'password' => bcrypt($userid),
                    'rincian' => "Kosong",
                    'verified' => 1
                ]);

                $verifyUser = VerifyUser::create([
                    'user_id' => $user->id,
                    'token' => sha1(time())
                ]);

                $params = [
                    'grant_type' => 'password',
                    'client_id' => $this->client->id,
                    'client_secret' => $this->client->secret,
                    'username' => $email,
                    'password' => $userid,
                    'scope' => '*'
                ];
        
                $request->request->add($params);
                $proxy = Request::create('oauth/token', 'POST');
        
                return Route::dispatch($proxy);

                // Mail::to($user->email)->send(new VerifyMail($user));
            } catch(Exception $e) {
                DB::rollBack();
                return response()->json(['message' => 'Login Google gagal karena terjadi kesalahan pada sistem kami: ' . $e->getMessage()]);
            }
            DB::commit();
        } else {
            // Invalid ID token
            return response()->json(array('message'=>'Invalid ID token'),200);
        }
    }
}
