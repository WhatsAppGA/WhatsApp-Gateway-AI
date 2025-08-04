<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShowMessageController extends Controller
{
    public function index(Request $request)
    {
        try {
            $table = $request->table;
            $column_message = $request->column;
            $data = DB::table($table)
                ->where('id', $request->id)
                ->first();
            $type = $data->type;
            // if not exists $data->keyword, fill keyword with name table
            $keyword = $data->keyword ?? 'Preview ' . $table;
            $message = ($data->$column_message);

            switch ($type) {
                case 'text':
                    $msg = [
                        'keyword' => $keyword,
                        'text' => json_decode($message)->text,
                    ];
                    break;
                case 'media':
                    $msg = [
                        'keyword' => $keyword,
                        'message' => json_decode($message),

                        // 'image' => json_decode($message)->image
                        //     ->url,
                    ];
                    break;
                case 'button':
                    $msg = [
                        'keyword' => $keyword,
                        'message' =>
                        json_decode($message)->text ??
                            json_decode($message)->caption,
                        'footer' => json_decode($message)->footer,
                        'buttons' => json_decode($message)
                            ->buttons,
                        'image' =>
                        json_decode($message)->image->url ??
                            null,
                    ];
                    break;
                case 'template':
                    $msg = [
                        'keyword' => $keyword,
                        'message' =>
                        json_decode($message)->text ??
                            json_decode($message)->caption,
                        'footer' => json_decode($message)->footer,
                        'templates' => json_decode($message)
                            ->templateButtons,
                        'image' =>
                        json_decode($message)->image->url ??
                            null,
                    ];
                    break;
                default:
                    return view('ajax.messages.emptyshow')->render();
                    break;
            }
            return view(
                'ajax.messages.' . $type . 'show',
                $msg
            )->render();
        } catch (\Throwable $th) {
            Log::error($th);
            return view('ajax.messages.emptyshow')->render();
        }
    }

    public function getFormByType($type, Request $request)
    {
        if ($request->ajax()) {
            return view('ajax.messages.form' . $type)->render();
        }
        return 'http request';
    }

    public function showEdit(Request $request)
    {

        $table = $request->table;
        $column_message = $request->column;
        $data = DB::table($table)
            ->where('id', $request->id)
            ->first();
        $type = $request->type;
        $keyword = $data->keyword ?? 'Preview ' . $table;
        $message = ($data->$column_message);

        switch ($type) {
            case 'text':
                $msg = [
                    'keyword' => $keyword,
                    'message' => json_decode($message)->text ?? '',
                    'id' =>  $request->id,
                ];
                break;
            case 'vcard':
                $decodedMessage = json_decode($message, true);
                $vcard = $decodedMessage['contacts']['contacts'][0]['vcard'];
                preg_match('/waid=(\d+)/', $vcard, $matches);
                $waid = $matches[1] ?? '';
                $msg = [
                    'keyword' => $keyword,
                    'contact' => $decodedMessage['contacts'] ?? '',
                    'waid' => $waid,
                    'id' =>  $request->id,
                ];
                break;
            case 'location':
                $msg = [
                    'keyword' => $keyword,
                    'message' => json_decode($message)->location ?? '',
                    'id' =>  $request->id,
                ];
                break;
            case 'sticker':
                $msg = [
                    'keyword' => $keyword,
                    'message' => json_decode($message) ?? '',
                    'id' =>  $request->id,
                ];
                break;
            case 'media':
                $msg = [
                    'keyword' => $keyword,
                    'message' => json_decode($message) ?? '',
                    'id' =>  $request->id,
                ];
                break;
            case 'button':
                $decodedMessage = json_decode($message);
                $buttons = [];

                if (isset($decodedMessage->buttons) && is_array($decodedMessage->buttons)) {
                    foreach ($decodedMessage->buttons as $index => $button) {
                        $buttonData = [
                            'displayText' => $button->displayText ?? '',
                            'type' => $button->type ?? '',
                        ];

                        if (isset($button->callButton)) {
                            $buttonData['type'] = 'call';
                            $buttonData['phoneNumber'] = $button->callButton->phoneNumber ?? '';
                        } elseif (isset($button->urlButton)) {
                            $buttonData['type'] = 'url';
                            $buttonData['url'] = $button->urlButton->url ?? '';
                        } elseif (isset($button->copyButton)) {
                            $buttonData['type'] = 'copy';
                            $buttonData['copyText'] = $button->copyButton->text ?? '';
                        } elseif (isset($button->postbackButton)) {
                            $buttonData['type'] = 'reply';
                        }

                        $buttons[] = $buttonData;
                    }
                }

                $msg = [
                    'keyword' => $keyword,
                    'message' => $decodedMessage->text ?? '',
                    'footer' => $decodedMessage->footer ?? '',
                    'buttons' => $buttons,
                    'image' => $decodedMessage->image->url ?? null,
                    'id' => $request->id,
                ];
                break;
            case 'list':
                $decodedMessage = json_decode($message);
                $url = $decodedMessage->image ? $decodedMessage->image->url : null;
                $msg = [
                    'message' => $decodedMessage->text ?? '',
                    'buttontext' => $decodedMessage->sections[0]->buttonText ?? '',
                    'footer' => $decodedMessage->footer ?? '',
                    'title' => $decodedMessage->sections[0]->list[0]->title ?? '',
                    'image' => $url,
                    'list' => []
                ];

                if (isset($decodedMessage->sections[0]->list[0]->rows)) {
                    foreach ($decodedMessage->sections[0]->list[0]->rows as $row) {
                        $msg['list'][] = $row->title ?? '';
                    }
                }

                $msg['id'] = $request->id;
                break;
            case 'template':
                $msg_template = json_decode($message)->templateButtons ?? [];
                foreach ($msg_template as $index => $array) {
                    $newTemplate[$index]['index'] = $array->index;
                    if (isset($array->callButton)) {
                        $newTemplate[$index]['type'] = "call|" . $array->callButton->displayText . "|" . $array->callButton->phoneNumber . "";
                    } else {
                        $newTemplate[$index]['type'] = "url|" . $array->urlButton->displayText . "|" . $array->urlButton->url . "";
                    }
                }
                $msg = [
                    'keyword' => $keyword,
                    'message' => json_decode($message)->text ?? '',
                    'footer' => json_decode($message)->footer ?? '',
                    'templates' => $newTemplate ?? [],
                    'image' => json_decode($message)->image->url ?? null,
                    'id' =>  $request->id,
                ];
                break;
            default:
                return view('ajax.messages.emptyshow')->render();
                break;
        }
        return view('ajax.messages.' . $type . 'edit', $msg)->render();
    }
}
