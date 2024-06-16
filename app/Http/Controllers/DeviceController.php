<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeviceRequest;
use App\Http\Requests\UpdateDeviceRequest;
use App\Models\Device;
use App\Traits\DotEnv;
use App\Traits\WhatsApp;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class DeviceController extends Controller
{
    use DotEnv;
    use WhatsApp;

    public function chats(Request $request, Device $device)
    {
        return view("chats.list", [
            'device' => $device,
            'chats' => $this->getChats($device)['data'],
        ]);
    }

    public function sendMessage(Request $request, Device $device)
    {
        $validated = $request->validate([
            "receiver" => "required|max:20",
            "message" => "required|max: 500",
        ]);

        $body["text"] = $this->formatText($request->message);
        $type = "plain-text";

        try {
            $response = $this->send(
                $body,
                $device,
                $request->receiver,
                $type,
                true
            );

            dd($response);
        } catch (Exception $e) {
            return response()->json(["error" => "Request Failed"], 401);
        }
    }

    public function chatHistory(Device $device)
    {
        $response = Cache::remember("groups_" . $device->id, 120, fn () => $this->getChats($device));
        if ($response["status"] == 200) {
            $data["chats"] = $response["data"];
            $data["device_name"] = $device->name;
            $data["phone"] = $device->phone;
            return response()->json($data);
        }

        $data["message"] = $response["message"];
        $data["status"] = $response["status"];

        return response()->json($data, 401);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDeviceRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Device $device)
    {
        $response = Http::post(env('WA_SERVER_URL').'/sessions/add', [
            'id' => 'device_'.$device->id,
            'isLegacy' => false,
        ]);

        if ($response->status() == 200) {
            $json = $response->json();
            $device->qr = $json['data']['qr'];
            $device->save();
        }

        return view('devices.show', compact('device'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Device $device)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDeviceRequest $request, Device $device)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Device $device)
    {
        //
    }
}
