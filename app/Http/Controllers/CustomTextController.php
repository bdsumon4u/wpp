<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Smstransaction;
use App\Models\Template;
use App\Rules\Phone;
use App\Traits\Whatsapp;
use Auth;
use Illuminate\Http\Request;

class CustomTextController extends Controller
{
    use Whatsapp;

    //return custom text message view page
    public function index()
    {

        $phoneCodes = file_exists('uploads/phonecode.json') ? json_decode(file_get_contents('uploads/phonecode.json')) : [];
        $devices = Device::where('user_id', Auth::id())->where('status', 1)->latest()->get();

        return view('user.singlesend.create', compact('phoneCodes', 'devices'));
    }

    //sent custom text msg request to api
    public function sentCustomText(Request $request, $type = 'plain-text')
    {
        // $validated = $request->validate([
        //     'phone'   => ['required', new Phone],
        //     'device'=>['required','numeric'],
        // ]);

        // if (getUserPlanData('messages_limit') == false) {
        //     return response()->json([
        //         'message'=>__('Maximum Monthly Messages Limit Exceeded')
        //     ],401);
        // }

        // if ($request->templatestatus) {
        //     if (getUserPlanData('template_limit') == false) {
        //         return response()->json([
        //             'message'=>__('Maximum Template Limit Exceeded')
        //         ],401);
        //     }
        // }

        $device = Device::findOrFail(3);

        $phone = '8801632388381';

        $whatsapp = $this->messageSend($request->all(), $device->id, $phone, $type);
        dd($whatsapp);
    }

    //creating record
    public function createTransaction($arr)
    {
        $trans = new Smstransaction;
        foreach ($arr as $key => $value) {
            $trans->$key = $value;
        }
        $trans->save();
    }
}
