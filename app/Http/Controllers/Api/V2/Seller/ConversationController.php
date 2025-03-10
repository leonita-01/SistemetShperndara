<?php

namespace App\Http\Controllers\Api\V2\Seller;

use App\Http\Resources\V2\MessageCollection;
use App\Http\Resources\V2\Seller\ConversationCollection;
use App\Http\Resources\V2\Seller\ConversationResource;
use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\BusinessSetting;
use App\Models\Message;

class ConversationController extends Controller
{
   
    public function index()
    {
        if (BusinessSetting::where('type', 'conversation_system')->first()->value == 1) {
            $conversations = Conversation::where('receiver_id', auth()->user()->id)
                ->orderBy('created_at', 'desc')
                ->get();
            return  ConversationResource::collection($conversations);
        } else {
            return $this->failed(translate('Conversation is disabled at this moment'));
        }
    }


    public function send_message_to_customer(Request $requrest)
    {
        $message = new Message();
        $conversation = Conversation::find($requrest->conversation_id)->where("receiver_id",auth()->user()->id)->first();

        if($conversation){
        $message->conversation_id = $requrest->conversation_id;
        $message->user_id = auth()->user()->id;
        $message->message = $requrest->message;
        $message->save();

        return $this->success(translate('Message send successfully'));
        }else{
            return $this->failed(translate('You cannot send this message.'));
        }
    }

    
    public function show($id)
    {
        $conversation = Conversation::findOrFail(decrypt($id));
        if ($conversation->sender_id == auth()->user()->id) {
            $conversation->sender_viewed = 1;
        } elseif ($conversation->receiver_id == auth()->user()->id) {
            $conversation->receiver_viewed = 1;
        }
        $conversation->save();

        return new ConversationCollection($conversation);
    }

    public function showMessages($id)
    {
        $conversation = Conversation::findOrFail($id);
        if ($conversation->receiver_id == auth()->user()->id) {
            $messages = Message::where("conversation_id",$id)->orderBy('created_at', 'DESC')->get();
            return new MessageCollection($messages);
        } else {
            return $this->failed(translate('You cannot see this message.'));
        }
    }

    
    public function destroy($id)
    {
        $conversation = Conversation::findOrFail(decrypt($id));
        foreach ($conversation->messages as $key => $message) {
            $message->delete();
        }
        if (Conversation::destroy(decrypt($id))) {
            flash(translate('Conversation has been deleted successfully'))->success();
            return back();
        }
    }
}
