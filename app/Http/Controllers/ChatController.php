<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $users = User::where('id', '!=', Auth::id())
            ->orderBy('name')
            ->get(['id','name']);

        return view('dashboard.chat.chat', compact('users'));
    }

    public function fetchMessages(User $user)
    {
        $authId = Auth::id();

        $messages = Message::where(function ($q) use ($authId, $user) {
                $q->where('sender_id', $authId)
                  ->where('receiver_id', $user->id)
                  ->where('deleted_by_sender', false);
            })
            ->orWhere(function ($q) use ($authId, $user) {
                $q->where('sender_id', $user->id)
                  ->where('receiver_id', $authId)
                  ->where('deleted_by_receiver', false);
            })
            ->orderBy('created_at')
            ->get();

        return response()->json($messages);
    }

    public function sendMessage(Request $request, User $user)
    {
        $request->validate([
            'content'    => 'nullable|string',
            'attachment' => 'nullable|file|max:10240',
        ]);

        if (!$request->content && !$request->hasFile('attachment')) {
            return response()->json(['error' => 'Mensaje vacío'], 422);
        }

        $data = [
            'sender_id'   => Auth::id(),
            'receiver_id' => $user->id,
            'content'     => $request->input('content'),
        ];


        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');

            $path = $file->store('chat-attachments', 'public');

            $data['attachment_path'] = $path;
            $data['attachment_type'] = $this->getFileType($file);
            $data['attachment_name'] = $file->getClientOriginalName();
        }

        $message = Message::create($data);

        broadcast(new \App\Events\MessageSent($message))->toOthers();

        return response()->json($message->load('sender'));
    }

    public function clearChat(User $user)
    {
        $authId = Auth::id();
        
        Message::where('sender_id', $authId)
            ->where('receiver_id', $user->id)
            ->update(['deleted_by_sender' => true]);

        Message::where('sender_id', $user->id)
            ->where('receiver_id', $authId)
            ->update(['deleted_by_receiver' => true]);

        return response()->json(['status' => 'Chat vaciado']);
    }

    private function getFileType($file)
    {
        $mime = $file->getMimeType();
        return str_contains($mime, 'image') ? 'image' : 'file';
    }
}