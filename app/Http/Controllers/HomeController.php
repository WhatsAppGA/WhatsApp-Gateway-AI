<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Models\Device;
class HomeController extends Controller
{


    public function index(Request $request)
    {
        $numbers = $request->user()->devices()->latest()->paginate(15);


        $user = $request->user()->withCount(['devices', 'campaigns'])->withCount(['blasts as blasts_pending' => function ($q) {
            return $q->where('status', 'pending');
        }])->withCount(['blasts as blasts_success' => function ($q) {
            return $q->where('status', 'success');
        }])->withCount(['blasts as blasts_failed' => function ($q) {
            return $q->where('status', 'failed');
        }])->withCount('messageHistories')->find($request->user()->id);



        $user['expired_subscription_status'] = $user->expiredSubscription;
        $user['subscription_status'] = $user->isExpiredSubscription ? 'Expired' : $user->active_subscription;
        return view('home', compact('numbers', 'user'));
    }

    public function store(Request $request)
    {

        $validate =  validator($request->all(), [
            'sender' => 'required|min:8|max:1000|unique:devices,body',
        ]);

        if ($request->user()->isExpiredSubscription) {
            return back()->with('alert', ['type' => 'danger', 'msg' => 'Your subscription has expired, please renew your subscription.']);
        }
        if ($validate->fails()) {
            return back()->with('alert', ['type' => 'danger', 'msg' => $validate->errors()->first()]);
        }

        if ($request->user()->limit_device <= $request->user()->devices()->count()) {
            return back()->with('alert', ['type' => 'danger', 'msg' => 'You have reached the limit of devices!']);
        }
        $request->user()->devices()->create(['body' => $request->sender, 'webhook' => $request->urlwebhook]);
        return back()->with('alert', ['type' => 'success', 'msg' => 'Devices Added!']);
    }


    public function destroy(Request $request)
    {
        try {
            //code...
            $device = $request->user()->devices()->find($request->deviceId);
            $device->delete();
            Session::forget('selectedDevice');
            if (!empty($device->body)) {
                $path = base_path('credentials/' . $device->body);
                if (file_exists($path)) {
                    File::deleteDirectory($path);
                }
            }
            return back()->with('alert', ['type' => 'success', 'msg' => 'Devices Deleted!']);
        } catch (\Throwable $th) {
            throw $th;
            return back()->with('alert', ['type' => 'danger', 'msg' => 'Something went wrong!']);
        }
    }


    public function setHook(Request $request)
    {
        clearCacheNode();
        return $request->user()->devices()->whereBody($request->number)->update(['webhook' => $request->webhook]);
    }

    public function setDelay(Request $request)
    {
        clearCacheNode();
        return $request->user()->devices()->whereBody($request->number)->update(['delay' => $request->delay]);
    }

    public function setHookRead(Request $request)
    {
        clearCacheNode();
        $request->user()->devices()->whereBody($request->id)->update(['wh_read' => $request->webhook_read]);
        return response()->json(['error' => false, 'msg' => __('Webhook read has been updated')]);
    }

    public function setHookReject(Request $request)
    {
        clearCacheNode();
     
        $upd =  $request->user()->devices()->whereBody($request->id)->update(['reject_call' => $request->webhook_reject_call]);
        return response()->json(['error' => false, 'msg' => __(' reject call has been updated')]);
    }

    public function setHookTyping(Request $request)
    {
        clearCacheNode();
        $request->user()->devices()->whereBody($request->id)->update(['wh_typing' => $request->webhook_typing]);
        return response()->json(['error' => false, 'msg' => __('Webhook typing has been updated')]);
    }

    public function setAvailable(Request $request)
    {
        clearCacheNode($request->id);
        $request->user()->devices()->whereBody($request->id)->update(['set_available' => $request->set_available]);
        return response()->json(['error' => false, 'msg' => __('Available has been updated')]);
    }


    public function setSelectedDeviceSession(Request $request)
    {
        $device = $request->user()->devices()->whereId($request->device)->first();
        if (!$device) {
            return response()->json(['error' => true, 'msg' => 'Device not found!']);
            Session::forget('selectedDevice');
        }
        session()->put('selectedDevice', [
            'device_id' => $device->id,
            'device_body' => $device->body,
        ]);
        return response()->json(['error' => false, 'msg' => 'Device selected!']);
    }
    public function deviceSettings(Device $number, Request $request)
    {
        if ($request->user()->id != $number->user_id) {
            return back()->with('alert', ['type' => 'danger', 'msg' => 'Number not found!']);
        }
    
        if ($request->has('saveSettings')) {
            $validatedData = $request->validate([
                'gemini_status' => 'required|in:enabled,disabled',
                'gemini_model' => 'required|in:gemini-1.5-pro,gemini-1.5-flash,gemini-1.5-flash-8b',
                'gemini_api_key' => 'nullable|string|max:255',
                'gemini_instructions' => 'nullable|string',
                'transcription_status' => 'required|in:enabled,disabled',
                'transcription_model' => 'required|in:whisper-large-v3-turbo,whisper-large-v3',
                'huggingface_api_key' => 'nullable|string|max:255',
                'auto_status_save' => 'required|in:enabled,disabled',
                'auto_status_forward' => 'required|in:enabled,disabled',
                'status_nudity_detection' => 'required|in:enabled,disabled',
                'chat_nudity_detection' => 'required|in:enabled,disabled',
            ]);
            $number->update($validatedData);
    
            return back()->with('alert', ['type' => 'success', 'msg' => 'Settings saved successfully!']);
        }
    
        return view('pages.user.deviceSettings', compact('number'));
    }
}
