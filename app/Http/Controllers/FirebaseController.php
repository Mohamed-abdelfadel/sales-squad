<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class FirebaseController extends Controller
{
    public function updateDeviceToken(Request $request)
    {
        if (auth()->check()) {
            auth()->user()->device_token = $request->token;
            auth()->user()->save();

            return response()->json(['message' => 'Token successfully stored.']);
        }

        return response()->json(['message' => 'User not authenticated.'], 401);
    }

    public function sendNotification(Request $request)
    {

        $url = 'https://fcm.googleapis.com/fcm/send';
        $fcmToken = array_filter(User::whereNotNull('device_token')->pluck('device_token')->all());
        $serverKey = 'AAAA8bR_ALk:APA91bELz5DbCxpvBEggn5b7RHYfCLXqNA6jFN7EJOJ98Tdzasb7H98_OLRzJz0AgA1L7lKjn8u-eH_oZMKdDJTUB0cOVEh0Uqp9eA-iQ6UXoWDE5AQiHF67DrrzSCJpnZ_tPuVugSA0';
        $data = [
            "registration_ids" => $fcmToken,
            "notification" => [
                "title" => $request->title,
                "body" => $request->body,
            ]
        ];

        $encodedData = json_encode($data);

        $headers = [
            'Authorization:key=' . $serverKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disabling SSL Certificate support temporarily
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
        // Execute post
        $result = curl_exec($ch);
        if (!$result) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);
        // FCM response
        return $result;
        return response()->json(["status" => "success"]);
    }
}
