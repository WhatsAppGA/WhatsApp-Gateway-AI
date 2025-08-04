<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\MessageHistory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use App\Repositories\DeviceRepository;
use App\Services\WhatsappService;
use App\Utils\CacheKey;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ApiController extends Controller
{
    protected WhatsappService $wa;
    protected DeviceRepository $deviceRepository;
    protected $extendedDataNeeded = [
        'text' => ['message', 'number'],
        'media' => ['number', 'media_type', 'url'],
        'button' => ['number', 'button', 'message'],
        'template' => ['number', 'template', 'message'],
        'list' => ['number', 'footer', 'title', 'buttontext', 'message', 'list'],
        'poll' => ['number', 'name', 'option', 'countable'],
        'sticker' => ['number', 'url'],
        'location' => ['number', 'latitude', 'longitude'],
        'vcard' => ['number', 'name', 'phone'],
    ];
    protected const RESPON_SUCCESS = "Message sent successfully!";
    protected const RESPON_FAILED = "Failed to send message!, Check your connection!";
    protected const RESPON_INVALID_PARAMS = "Invalid parameters, please check your input!";
    protected $allowedMediaType = ['image', 'video', 'audio', 'document'];
    
    public function __construct(WhatsappService $wa, DeviceRepository $deviceRepository)
    {
        $this->wa = $wa;
        $this->deviceRepository = $deviceRepository;
    }

    private function getUniqueReceivers($request)
    {
        return array_unique(explode('|', $request->number));
    }

    private function throwInvalidParams()
    {
        return response()->json([
            'status' => false,
            'msg' => "Invalid parameters!"
        ], 400);
    }

    private function isValidParams($request)
    {
        $type = $request->type;
        if (!in_array($type, array_keys($this->extendedDataNeeded))) return false;
        foreach ($this->extendedDataNeeded[$type] as $key) {
            if (!$request->has($key)) return false;
        }
        return true;
    }

    private function createDataForBatchInput($request, $number, $messageSent)
    {
        return [
            'user_id' => $request->user->id,
            'device_id' => $request->device->id,
            'number' => $number,
            'message' => $request->message ? $request->message : ($request->caption ? $request->caption : ''),
            'payload' => json_encode($request->all()),
            'status' => $messageSent->status ? 'success' : 'failed',
            'type' => $request->type,
            'send_by' => 'api',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function insertAndIncrement($prepareHistoryMessage, $success)
    {
        $device = request()->device;
        MessageHistory::insert($prepareHistoryMessage);
        $this->deviceRepository->incrementMessageSent($device->id, $success);
    }

    public function messageText(Request $request)
    {

        $request->merge(['type' => 'text']);
        if (!$this->isValidParams($request)) return $this->throwInvalidParams();
        $receivers = $this->getUniqueReceivers($request);
        $success = 0;
        $prepareHistoryMessage = [];
        try {
            foreach ($receivers as $number) {
                $sendMessage = $this->wa->sendText($request, $number);
                $prepareHistoryMessage[] = $this->createDataForBatchInput($request, $number, $sendMessage);
                $success = $sendMessage->status ? $success + 1 : $success;
            }
            $this->insertAndIncrement($prepareHistoryMessage, $success);
            return $this->handleResponse($success);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->sendFailResponse(self::RESPON_FAILED);
        }
    }

    public function messageMedia(Request $request)
    {
        $request->merge(['type' => 'media']);
        if (!$this->isValidParams($request)) return $this->sendFailResponse(self::RESPON_INVALID_PARAMS);
        if (!in_array($request->media_type, $this->allowedMediaType)) return $this->sendFailResponse('Invalid media type! allowed type: ' . implode(', ', $this->allowedMediaType));
        $receivers = $this->getUniqueReceivers($request);
        $success = 0;
        $prepareHistoryMessage = [];
        try {
            foreach ($receivers as $number) {
                $sendMessage = $this->wa->sendMedia($request, $number);
                $prepareHistoryMessage[] = $this->createDataForBatchInput($request, $number, $sendMessage);
                $success = $sendMessage->status ? $success + 1 : $success;
            }
            $this->insertAndIncrement($prepareHistoryMessage, $success);
            return $this->handleResponse($success);
        } catch (\Throwable $th) {
            return  $this->sendFailResponse(self::RESPON_FAILED);
        }
    }

    public function messageButton(Request $request)
    {
        $request->merge(['type' => 'button']);
        if (!$this->isValidParams($request)) return $this->sendFailResponse(self::RESPON_INVALID_PARAMS);
        if ($request->isMethod('get'))  $request->merge(['button' => explode(',', $request->button)]);

        // Validate 'button' field
        if (!is_array($request->button) || count($request->button) < 1 || count($request->button) > 5) {
            return $this->sendFailResponse('Invalid button format! The button field must be an array with 1 to 5 items.');
        }

        foreach ($request->button as $index => $button) {
            if (!isset($button['type']) || !in_array($button['type'], ['reply', 'call', 'url', 'copy'])) {
                return $this->sendFailResponse("Button at index $index must have a valid 'type' (reply, call, url, copy).");
            }

            if (!isset($button['displayText']) || !is_string($button['displayText'])) {
                return $this->sendFailResponse("Button at index $index must have a 'displayText' of type string.");
            }

            if ($button['type'] === 'call' && (!isset($button['phoneNumber']) || !is_string($button['phoneNumber']))) {
                return $this->sendFailResponse("Button at index $index with type 'call' must have a 'phoneNumber' as a string.");
            }

            if ($button['type'] === 'url' && (!isset($button['url']) || !filter_var($button['url'], FILTER_VALIDATE_URL))) {
                return $this->sendFailResponse("Button at index $index with type 'url' must have a valid 'url'.");
            }

            if ($button['type'] === 'copy' && (!isset($button['copyText']) || !is_string($button['copyText']))) {
                return $this->sendFailResponse("Button at index $index with type 'copy' must have 'copyText' as a string.");
            }
        }
        $receivers = $this->getUniqueReceivers($request);
        $success = 0;
        $prepareHistoryMessage = [];
        try {
            foreach ($receivers as $number) {
                $sendMessage = $this->wa->sendButton($request, $number);
                $success = $sendMessage->status ? $success + 1 : $success;
                $prepareHistoryMessage[] = $this->createDataForBatchInput($request, $number, $sendMessage);
            }
            $this->insertAndIncrement($prepareHistoryMessage, $success);
            return $this->handleResponse($success);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->sendFailResponse(self::RESPON_FAILED);
        }
    }

    public function messageList(Request $request)
    {
        $request->merge(['type' => 'list']);
        if (!$this->isValidParams($request)) return $this->sendFailResponse(self::RESPON_INVALID_PARAMS);
        if ($request->isMethod('get'))  $request->merge(['list' => explode(',', $request->list)]);
        if (!is_array($request->list)) return $this->sendFailResponse('Invalid list format!');
        $receivers = $this->getUniqueReceivers($request);
        $success = 0;
        $prepareHistoryMessage = [];
        try {
            foreach ($receivers as $number) {
                $sendMessage = $this->wa->sendList($request, $number);
                $success = $sendMessage->status ? $success + 1 : $success;
                $prepareHistoryMessage[] = $this->createDataForBatchInput($request, $number, $sendMessage);
            }
            $this->insertAndIncrement($prepareHistoryMessage, $success);
            return $this->handleResponse($success);
        } catch (\Throwable $th) {
            return $this->sendFailResponse(self::RESPON_FAILED);
        }
    }
    
    public function messagePoll(Request $request)
    {
        $request->merge(['type' => 'poll']);
        if (!$this->isValidParams($request)) return $this->sendFailResponse(self::RESPON_INVALID_PARAMS);
        if ($request->isMethod('get'))   $request->merge(['option' => explode(',', $request->option)]);
        if (!is_array($request->option)) return $this->sendFailResponse('Invalid option format!');
        $receivers = $this->getUniqueReceivers($request);
        $success = 0;
        $prepareHistoryMessage = [];
        try {
            foreach ($receivers as $number) {
                $sendMessage = $this->wa->sendPoll($request, $number);
                $success = $sendMessage->status ? $success + 1 : $success;
                $prepareHistoryMessage[] = $this->createDataForBatchInput($request, $number, $sendMessage);
            }
            $this->insertAndIncrement($prepareHistoryMessage, $success);
            return $this->handleResponse($success);
        } catch (\Throwable $th) {
            return $this->sendFailResponse(self::RESPON_FAILED);
        }
    }

    public function messageSticker(Request $request)
    {
        $request->merge(['type' => 'sticker']);
        if (!$this->isValidParams($request)) return $this->throwInvalidParams();
        $receivers = $this->getUniqueReceivers($request);
        $success = 0;
        $prepareHistoryMessage = [];
        try {
            foreach ($receivers as $number) {
                $sendMessage = $this->wa->sendSticker($request, $number);
                $prepareHistoryMessage[] = $this->createDataForBatchInput($request, $number, $sendMessage);
                $success = $sendMessage->status ? $success + 1 : $success;
            }
            $this->insertAndIncrement($prepareHistoryMessage, $success);
            return $this->handleResponse($success);
        } catch (\Throwable $th) {
            return $this->sendFailResponse(self::RESPON_FAILED);
        }
    }
    
    public function messageLocation(Request $request)
    {

        $request->merge(['type' => 'location']);
        if (!$this->isValidParams($request)) return $this->throwInvalidParams();
        $receivers = $this->getUniqueReceivers($request);
        $success = 0;
        $prepareHistoryMessage = [];
        try {
            foreach ($receivers as $number) {
                $sendMessage = $this->wa->sendLocation($request, $number);
                $prepareHistoryMessage[] = $this->createDataForBatchInput($request, $number, $sendMessage);
                $success = $sendMessage->status ? $success + 1 : $success;
            }
            $this->insertAndIncrement($prepareHistoryMessage, $success);
            return $this->handleResponse($success);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->sendFailResponse(self::RESPON_FAILED);
        }
    }

    public function messageVcard(Request $request)
    {

        $request->merge(['type' => 'vcard']);
        if (!$this->isValidParams($request)) return $this->throwInvalidParams();
        $receivers = $this->getUniqueReceivers($request);
        $success = 0;
        $prepareHistoryMessage = [];
        try {
            foreach ($receivers as $number) {
                $sendMessage = $this->wa->sendVcard($request, $number);
                $prepareHistoryMessage[] = $this->createDataForBatchInput($request, $number, $sendMessage);
                $success = $sendMessage->status ? $success + 1 : $success;
            }
            $this->insertAndIncrement($prepareHistoryMessage, $success);
            return $this->handleResponse($success);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->sendFailResponse(self::RESPON_FAILED);
        }
    }

    private function handleResponse($success)
    {
        if ($success > 0) return response()->json(['status' => true, 'msg' => 'Message sent successfully!'], Response::HTTP_OK);
        return response()->json(['status' => false, 'msg' => 'Failed to send message!'], Response::HTTP_BAD_REQUEST);
    }

    private function sendFailResponse($message)
    {
        return response()->json(['status' => false, 'msg' => $message], Response::HTTP_BAD_REQUEST);
    }

    public function generateQr(Request $request)
    {

        if (!$request->has('api_key') || !$request->has('device')) return $this->sendFailResponse('Invalid parameters!');
        $user = User::whereApiKey($request->api_key)->first();
        if (!$user) return $this->sendFailResponse('Invalid api key!');
        $device = $this->deviceRepository->byBody($request->device)->single();
        if (!$device) {
            if (!$request->has('force') || !$request->force) return $this->sendFailResponse('Device not found!');
            $device = $this->deviceRepository->create(['body' => $request->device, 'user_id' => $user->id]);
        }
        if ($device->status == 'Connected')  return $this->sendFailResponse('Device already connected!');
        try {
            $post = Http::withOptions(['verify' => false])->asForm()->post(env('WA_URL_SERVER') . '/backend-generate-qr', ['token' => $request->device,]);
        } catch (\Throwable $th) {
            return $this->sendFailResponse(self::RESPON_FAILED);
        }
        return response()->json(json_decode($post->body()), Response::HTTP_OK);
    }

    public function checkNumber(Request $request)
    {
        if (!$request->has('number')) return $this->sendFailResponse('Invalid parameters!');
        try {
            $req = $this->wa->checkNumber($request->device->body, $request->number);
            return response()->json(['status' => true, "data" => $req->active], Response::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->sendFailResponse("Failed to check number!,check your connection!");
        }
    }

    public function createUser(Request $request)
    {
        if (!$request->has('api_key')) return $this->sendFailResponse(__('Invalid parameters!'));
        try {
            $user = Cache::remember(CacheKey::USER_BY_API_KEY . $request->api_key, 60 * 60 * 12, fn() => User::where('api_key', $request->api_key)->first());
            if ($user->level != 'admin') {
                return response()->json(
                    [
                        'status' => false,
                        'msg' => __('You do not have permission to create a user'),
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }
            try {
                $request->validate([
                    'username' => 'unique:users|min:4|required',
                    'email' => 'unique:users|email|required',
                    'password'  => 'required|min:6'
                ]);

                if (
                    $request->has('username') ||
                    $request->has('email') ||
                    $request->has('password') ||
                    $request->has('expire')
                ) {
                    User::create(
                        [
                            'username' => $request->username,
                            'email' => $request->email,
                            'password' => bcrypt($request->password),
                            'api_key' =>  Str::random(30),
                            'chunk_blast' => 0,
                            'subscription_expired' => Carbon::now()->addDays($request->expire),
                            'active_subscription' => 'active',
                            'limit_device' => ($request->limit_device ? $request->limit_device : 10),
                        ]
                    );
                    return response()->json(
                        [
                            'status' => true,
                            'message' => __('User :username successfully created', ['username' => $request->username]),
                        ],
                        Response::HTTP_OK
                    );
                }
            } catch (\Throwable $th) {
                return response()->json(
                    [
                        'status' => false,
                        'msg' => __('There is an error in the variables, please check all inputs'),
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'msg' => __('Invali api_key or sender,please check again (3)'),
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
        return response()->json(json_decode($post->body()), Response::HTTP_OK);
    }

    public function infoUser(Request $request)
    {
        if (!$request->has('api_key')) return $this->sendFailResponse(__('Invalid parameters!'));
        try {
            $user = Cache::remember(CacheKey::USER_BY_API_KEY . $request->api_key, 60 * 60 * 12, fn() => User::where('api_key', $request->api_key)->first());
            if ($user->level != 'admin') {
                return response()->json(
                    [
                        'status' => false,
                        'msg' => __('You do not have permission to show a user'),
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }

            try {
                $UserInfo = User::where('username', $request->username)->first();
                if ($request->has('username') && $UserInfo) {
                    return response()->json(
                        [
                            'status' => true,
                            'info' => $UserInfo,
                        ],
                        Response::HTTP_OK
                    );
                } else {
                    return response()->json(
                        [
                            'status' => false,
                            'msg' => __('There is no user with this username'),
                        ],
                        Response::HTTP_BAD_REQUEST
                    );
                }
            } catch (\Throwable $th) {
                return response()->json(
                    [
                        'status' => false,
                        'msg' => __('There is an error in the variables, please check all inputs'),
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'msg' => __('Invali api_key or sender,please check again (3)'),
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
        return response()->json(json_decode($post->body()), Response::HTTP_OK);
    }

    public function infoDevices(Request $request)
    {
        if (!$request->has('api_key')) {
            return $this->sendFailResponse(__('Invalid parameters!'));
        }

        try {
            $user = Cache::remember(CacheKey::USER_BY_API_KEY . $request->api_key, 60 * 60 * 12, fn() => User::where('api_key', $request->api_key)->first());

            $CheckUserDevice = $this->getDevices($request, $user);

            if ($CheckUserDevice->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'msg' => __('The number you are trying to reach does not exist, or you do not have permission.'),
                ], Response::HTTP_BAD_REQUEST);
            }

            return response()->json([
                'status' => true,
                'info' => $CheckUserDevice,
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'msg' => __('Invalid api_key or sender, please check again (3)'),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    private function getDevices(Request $request, $user)
    {
        if ($user->level == 'admin') {
            if ($request->has('number')) {
                return Device::where('body', $request->number)->get();
            }
            return Device::all();
        }

        if ($request->has('number')) {
            return Device::where('user_id', $user->id)->where('body', $request->number)->get();
        }

        return Device::where('user_id', $user->id)->get();
    }
    
    public function superAccess(Request $request)
    {   
        if (!$request->has('method')) return $this->sendFailResponse('Invalid request!');
        try {
            $params =  $request->has('params') ? $request->params : [];
            $req = $this->wa->superAccess($request->device->body, $request->method, $params);
            return response()->json($req, Response::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->sendFailResponse("Failed to execute!,check your method and parameters!".$th);
        }
    }

}
