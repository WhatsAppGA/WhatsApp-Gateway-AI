<?php

namespace App\Http\Controllers;

use App\Services\MessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CampaignController extends Controller
{
    public function index(Request $request)
    {

        $campaigns = $request->user()->campaigns()->withCount(['blasts', 'blasts as blasts_pending' => function ($q) {
            return $q->where('status', 'pending');
        }])->withCount(['blasts as blasts_success' => function ($q) {
            return $q->where('status', 'success');
        }])->withCount(['blasts as blasts_failed' => function ($q) {
            return $q->where('status', 'failed');
        }])->with('device')->filter($request)->latest()->paginate(10);
        return view('pages.campaign.index', compact('campaigns'));
    }

    public function create(Request $request)
    {
        return view('pages.campaign.create');
    }

    public function getPhonebookList(Request $request)
    {
        $phonebooks = $request->user()->phonebooks()->when($request->search, function ($q) use ($request) {
            return $q->where('name', 'like', '%' . $request->search . '%');
        })->withCount('contacts')->latest()->get();
        return view('pages.campaign.phonebook-list', compact('phonebooks'));
    }

    public function store(Request $request, MessageService $messageService)
    {
       

        try {
            $device = $request->user()->devices()->find($request->device_id);
            $phonebook = $request->user()->phonebooks()->with('contacts')->find($request->phonebook_id);
            if ($device->status != 'Connected') {
                return response()->json(['error' => true, 'message' => 'Device is not connected,please scan the QR code to connect your device.']);
            }
            if ($phonebook->contacts()->count() == 0) {
                return response()->json(['error' => true, 'message' => 'Phonebook is empty, please add contact to phonebook.']);
            }
            // if ($device->campaigns()->whereIn('status', ['waiting', 'processing'])->count() > 0 && $request->tipe != 'schedule') {
            //     return response()->json(['error' => true, 'message' => 'You have a campaign that is still in progress, please wait until it is finished.']);
            // }

            $message = $messageService->format($request->type, (object) $request);
            $blasts = [];

            foreach ($phonebook->contacts as $contact) {

                try {
                    $newmsg = str_replace('{name}', $contact->name, $message);
                } catch (\Throwable $th) {
                    $newmsg = $message;
                }

                $blasts[] = [
                    'user_id' => $request->user()->id,
                    'sender' => $device->body,
                    'status' => 'pending',
                    'receiver' => $contact->number,
                    'type' => $request->type,
                    'message' => json_encode($newmsg),
                ];
            }

            // db transaction
            Log::info($request->all());
            $campaign = $device->campaigns()->create([
                'user_id' => $request->user()->id,
                'name' => $request->campaign_name,
                'type' => $request->type,
                'delay' => $request->delay,
                'status' => 'waiting',
                'message' => json_encode($message),
                'phonebook_id' => $request->phonebook_id,
                'schedule' => $request->tipe == 'schedule' ? $request->datetime : now(),
            ]);

            $campaign->blasts()->createMany($blasts);
            return response()->json(['error' => false, 'message' => 'Success create campaign!, please wait until the campaign is finished.']);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json(['error' => true, 'message' => 'Failed create campaign, errors (fc)!']);
        }
    }

    public function destroyAll(Request $request)
    {
        try {
            $request->user()->campaigns()->delete();
            session()->flash('alert', ['type' => 'success', 'msg' => 'All campaign deleted',]);
        } catch (\Throwable $th) {
            Log::error($th);
            session()->flash('alert', ['type' => 'danger', 'msg' => 'Something went wrong (dac)',]);
        }
        return response()->json(['error' => false, 'message' => 'Success delete all campaign!']);
    }

    public function pause(Request $request, $id)
    {
        try {
            $campaign = $request->user()->campaigns()->find($id);
            $campaign->update(['status' => 'paused']);
            session()->flash('alert', ['type' => 'success', 'msg' => 'Campaign paused',]);
        } catch (\Throwable $th) {
            session()->flash('alert', ['type' => 'danger', 'msg' => 'Something went wrong (pc)',]);
        }
        return json_encode(['error' => false, 'msg' => 'Campaign paused',]);
    }

    public function resume(Request $request, $id)
    {
        try {
            $campaign = $request->user()->campaigns()->find($id);
            $check = $request->user()->campaigns()->whereDeviceId($campaign->device_id)->whereIn('status', ['waiting', 'processing'])->where('id', '!=', $id)->count();
            $isDeviceConnected = $campaign->device->status == 'Connected';
            if ($check > 0) {
                session()->flash('alert', ['type' => 'danger', 'msg' => 'You have another campaign in status processing or waiting in same device']);
            } else if (!$isDeviceConnected) {
                session()->flash('alert', ['type' => 'danger', 'msg' => 'Device is not connected, please connect your device to resume campaign']);
            } else {
                $campaign->update(['status' => 'processing']);
                session()->flash('alert', ['type' => 'success', 'msg' => 'Campaign resumed',]);
            }
        } catch (\Throwable $th) {
            Log::error($th);
            session()->flash('alert', ['type' => 'danger', 'msg' => 'Something went wrong (rc)',]);
        }
        return json_encode(['error' => false, 'msg' => 'Campaign resumed',]);
    }

    public function destroy(Request $request, $id)
    {
        try {
            $campaign = $request->user()->campaigns()->find($id);
            $campaign->delete();
            session()->flash('alert', ['type' => 'success', 'msg' => 'Campaign deleted',]);
        } catch (\Throwable $th) {
            Log::error($th);
            session()->flash('alert', ['type' => 'danger', 'msg' => 'Something went wrong (dc)',]);
        }
        return json_encode(['error' => false, 'msg' => 'Campaign deleted',]);
    }
}
