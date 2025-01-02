<?php

namespace App\Http\Controllers;

use App\Models\NotificationList;
use App\Models\PushNotification;
use Illuminate\Http\Request;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;

class PushNotificationController extends Controller
{

    public function getToken(){

        $token = session()->get("g_token");
        $expires_in = session()->get('g_token_expiration');

        if($expires_in && $expires_in > time()){
            return $token;
        }

        $credentialsFilePath = storage_path("app/service-account.json");
        
        $client = new GoogleClient();

        $client->setAuthConfig($credentialsFilePath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
        $res = $client->getAccessToken();

        $token = $res['access_token'];
        $expires_in = $res['created']+$res['expires_in'];

        session()->put("g_token", $token);
        session()->put("g_token_expiration", $expires_in);


        return $token;
    }

    public function index()
    {
        
        return view('backend.marketing.push_notification.index');
    }


    public function sendPushNotification(Request $request){

        $projectID = "muslimtown";
        $topicName = "MuslimtownNotification";
        $notificationTitle = $request->title;
        $notificationBody = $request->description;

        try{
            $token = $this->getToken();

            $notification = NotificationList::create([
                "title" => $notificationTitle,
                "description" => $notificationBody,
                "product_slug" => $request->product_slug
            ]);

            $response = Http::withHeaders([
                "Content-Type" => "application/json",
                "Authorization" => "Bearer $token"
            ])->post("https://fcm.googleapis.com/v1/projects/$projectID/messages:send", [
            "message" => [
                    "topic"=> "$topicName",
                    "notification"=> [
                        "title"=> "$notificationTitle",
                        "body"=> "$notificationBody",
                    ],
                    "data"=> [
                        "id" => "$notification->id"
                        // Pass Notification Data Here
                    ]
            ]
            ]);
        
            if($response->ok()){
                return response()->json(true, 200);
            }else{
                return response()->json($response->body(), 500);
            }
        }catch(\Exception $e){
            throw $e;
        }
        
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        return $request->all();
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }
}
