<?php

namespace App\Traits;

use App\Models\Device;
use App\Models\Template;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

trait WhatsApp
{
    private function send($data, $device, $receiver, $type, $filter = false, $delay = 0, $group = false)
    {
        $delay = $delay == 0 ? env('DELAY_TIME', 1000) : $delay;
        sleep($delay < 500 ? 1 : round($delay / 1000));

        try {
            $response = Http::post(env('WA_SERVER_URL').'/'.($group ? 'groups' : 'chats').'/send?id=device_' . $device->id, [
                'message' => $filter == false ? $this->formatArray($data, $data['message'], $type) : $data,
                'receiver' => $receiver,
                'delay' => 0,
            ]);

            if (($status = $response->status()) != 200) {
                $responseBody = json_decode($response->body());
                $responseData['message'] = $responseBody->message;
                $responseData['status'] = $status;
            } else {
                $responseData['status'] = 200;
            }

            return $responseData;
        } catch (Exception $e) {
            $responseData['status'] = 403;

            return $responseData;
        }

    }

    private function getChats($device)
    {
        $response = Http::get(env('WA_SERVER_URL').'/chats?id=device_' . $device->id);

        if (($status = $response->status()) != 200) {
            $responseBody = json_decode($response->body());
            $responseData['message'] = $responseBody->message;
            $responseData['status'] = $status;
        } else {
            $responseBody = json_decode($response->body());
            $colllections = collect($responseBody->data);
            $contacts = $colllections->map(function ($item) {
                $phone = explode('@', $item->id);
                $data['number'] = $phone[0] ?? null;
                $data['unread'] = $item->unreadCount ?? 0;
                $data['timestamp'] = $item->conversationTimestamp ?? 0;

                return $data;
            });

            $responseData['status'] = 200;
            $responseData['data'] = $contacts;
        }

        return $responseData;

    }

    public function getGroupList($device)
    {
        $response = Http::get(env('WA_SERVER_URL').'/groups?id='.$device->id);

        if (($status = $response->status()) != 200) {
            $responseBody = json_decode($response->body());
            $responseData['message'] = $responseBody->message;
            $responseData['status'] = $status;
        } else {
            $responseBody = json_decode($response->body());
            $colllections = collect($responseBody->data);

            $contacts = $colllections->map(function ($item) {

                $data['name'] = $item->name;
                $data['id'] = $item->id;

                return $data;
            });

            $responseData['status'] = 200;
            $responseData['data'] = $contacts;
        }

        return $responseData;
    }

    private function formatArray($data, $message, $type)
    {
        if ($type == 'plain-text') {
            return ['text' => $message];
        }
        
        if ($type == 'text-with-media') {
            $explode = explode('.', $data['attachment']);
            $file_type = strtolower(end($explode));
            $extentions = [
                'jpg' => 'image',
                'jpeg' => 'image',
                'png' => 'image',
                'webp' => 'image',
                'pdf' => 'document',
                'docx' => 'document',
                'xlsx' => 'document',
                'csv' => 'document',
                'txt' => 'document',
            ];

            return [
                'caption' => $message,
                $extentions[$file_type] => [
                    'url' => asset($data['attachment']),
                ],
            ];
        }
        
        if ($type == 'text-with-button') {
            $buttons = [];
            foreach ($data['buttons'] as $key => $button) {
                $buttons = [
                    'buttonId' => 'id'.$key,
                    'buttonText' => ['displayText' => $button],
                    'type' => 1,
                ];
            }

            return [
                'buttons' => $buttons,
                'text' => $message,
                'footer' => $data['footer_text'],
                'headerType' => 1,
            ];
        }
        
        if ($type == 'text-with-template') {
            $templateButtons = [];
            foreach ($data['buttons'] as $key => $button) {
                [$button_type, $button_action_content] = match ($button['type']) {
                    'urlButton' => ['url', $button['action']],
                    'callButton' => ['phoneNumber', $button['action']],
                    default => ['id', 'action-id-'.$key],
                };

                $templateButtons[] = [
                    'index' => $key,
                    $button['type'] => [
                        'displayText' => $button['displaytext'],
                        $button_type => $button_action_content,
                    ],
                ];
            }

            return [
                'text' => $message,
                'footer' => $data['footer_text'],
                'templateButtons' => $templateButtons,
            ];
        }
        
        if ($type == 'text-with-location') {
            return [
                'location' => [
                    'degreesLatitude' => $data['degreesLatitude'],
                    'degreesLongitude' => $data['degreesLongitude'],
                ],
            ];
        }
        
        if ($type == 'text-with-vcard') {
            $vcard = 'BEGIN:VCARD\n' // metadata of the contact card
            .'VERSION:3.0\n'
            .'FN:'.$data['full_name'].'\n' // full name
            .'ORG:'.$data['org_name'].';\n' // the organization of the contact
            .'TEL;type=CELL;type=VOICE;waid='.$data['contact_number'].':'.$data['wa_number'].'\n' // WhatsApp ID + phone number
            .'END:VCARD';

            return [
                'contacts' => [
                    'displayName' => 'maruf',
                    'contacts' => [[$vcard]],
                ],
            ];
        }

        if ($type == 'text-with-list') {
            $templateButtons = [];
            foreach ($data['section'] as $section_key => $sections) {
                $rows = [];
                foreach ($sections['value'] as $value_key => $value) {
                    $rowArr['title'] = $value['title'];
                    $rowArr['rowId'] = 'option-'.$section_key.'-'.$value_key;

                    if ($value['description'] != null) {
                        $rowArr['description'] = $value['description'];
                    }
                    
                    $rows[] = $rowArr;
                    $rowArr = [];
                }

                $row['title'] = $sections['title'];
                $row['rows'] = $rows;

                $templateButtons[] = $row;
                $row = [];
            }

            return [
                'text' => $message,
                'footer' => $data['footer_text'],
                'title' => $data['header_title'],
                'buttonText' => $data['button_text'],
                'sections' => $templateButtons,
            ];

        }

        return [];
    }

    private function saveTemplate($data, $message, $type, $user_id, $template_id = null)
    {
        // if ($template_id == null) {
        //     $template = new Template;
        // } else {
        //     $template = Template::findorFail($template_id);
        //     $template->status = isset($data['status']) ? 1 : 0;
        // }

        // $template->title = $data['template_name'];
        // $template->user_id = $user_id;
        // $template->body = $this->formatArray($data, $message, $type);
        // $template->type = $type;
        // $template->save();

        // return true;
    }

    private function saveFile(Request $request, $input)
    {
        // $file = $request->file($input);
        // $ext = $file->extension();
        // $filename = now()->timestamp.'.'.$ext;

        // $path = 'uploads/message/'.\Auth::id().date('/y').'/'.date('m').'/';
        // $filePath = $path.$filename;

        // Storage::put($filePath, file_get_contents($file));

        // return Storage::url($filePath);
    }

    private function groupMetaData($group, $device)
    {
        try {
            $response = Http::get(env('WA_SERVER_URL') . '/groups/meta/' . $group->id . '?id=device_' . $device->id);

            if (($status = $response->status()) != 200) {
                $responseBody = json_decode($response->body());
                $responseData['message'] = $responseBody->message;
                $responseData['status'] = $status;
            } else {
                $responseData['status'] = 200;
                $responseData['data'] = json_decode($response->body());

            }

            return $responseData;
        } catch (Exception $e) {
            $responseData['status'] = 403;

            return $responseData;
        }
    }

    private function formatText($context = '', $contact_data = null, $sender_data = null)
    {
        if ($context == '') {
            return $context;
        }

        if ($contact_data != null) {
            $name = $contact_data['name'] ?? '';
            $phone = $contact_data['phone'] ?? '';

            $context = str_replace('{name}', $name, $context);
            $context = str_replace('{phone_number}', $phone, $context);

        }

        if ($sender_data != null) {
            $sender_name = $sender_data['name'] ?? '';
            $sender_phone = $sender_data['phone'] ?? '';
            $sender_email = $sender_data['email'] ?? '';

            $context = str_replace('{my_name}', $sender_name, $context);
            $context = str_replace('{my_contact_number}', $sender_phone, $context);
            $context = str_replace('{my_email}', $sender_email, $context);
        }

        return $context;

    }

    private function formatCustomText($context = '', $replaceableData = [])
    {
        foreach ($replaceableData ?? [] as $key => $value) {
            $context = str_replace($key, $value, $context);
        }

        return $context;

    }
}
