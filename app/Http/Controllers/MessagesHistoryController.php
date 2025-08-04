<?php

namespace App\Http\Controllers;

use App\Models\MessageHistory;
use App\Services\WhatsappService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MessagesHistoryController extends Controller
{
    public function index(Request $request)
    {

        $messages = $request->user()->messageHistories()->with('device')->latest()->paginate(15);
        return view('pages.histories.message', compact('messages'));
    }

    public function resend(Request $request, WhatsappService $wa)
    {
        try {
            $history = MessageHistory::find($request->id);
            $device = $history->device;

            if ($history->status == 'success') {
                return response()->json(['error' => true, 'msg' => 'Message already sent, refresh page to update status']);
            }
            if ($device->status != 'Connected') {
                return response()->json(['error' => true, 'msg' => 'Device is not connected']);
            }

            $params = json_decode($history->payload);
            if ($history->type == 'text') {
                $res = $wa->sendText($params, $history->number);
            } else if ($history->type == 'media') {
                $res = $wa->sendMedia($params, $history->number);
            } else if ($history->type == 'button') {
                $res = $wa->sendButton($params, $history->number);
            } else if ($history->type == 'template') {
                $res = $wa->sendTemplate($params, $history->number);
            } else if ($history->type == 'list') {
                $res = $wa->sendList($params, $history->number);
            }


            if ($res->status) {
                $history->update(['status' => 'success']);
                return response()->json(['error' => false, 'msg' => 'Resend message success']);
            } else {
                return response()->json(['error' => true, 'msg' => 'Failed to resend message to this number,make sure the receiver is registered on whatsapp']);
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json(['error' => true, 'msg' => 'Something went wrong while resending message']);
        }
    }

    public function clearAll(Request $request)
    {
        $request->user()->messageHistories()->delete();
        return response()->json(['message' => 'success']);
    }
}
