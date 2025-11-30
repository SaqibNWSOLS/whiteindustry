<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::where('user_id', $request->user()->id)
            ->orWhereNull('user_id');

        if ($request->has('unread')) {
            $query->where('is_read', false);
        }
        $notifications=$query->paginate(20);
            
        return view('notifications.index', compact('notifications'));
    }

    public function markAllRead()
    {
 Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);        
        return redirect()->route('notifications.index')
            ->with('success', 'All notifications marked as read');
    }

     public function deleteAll()
    {
 Notification::where('user_id', auth()->id())
            ->delete();        
        return redirect()->route('notifications.index')
            ->with('success', 'All notifications marked as read');
    }


    public function toggleRead($id)
    {
        $notification=Notification::where('id',$id)->first();

        $notification->markAsRead();
        
        return redirect()->route('notifications.index')
            ->with('success', 'Notification status updated');
    }

    public function delete($id)
    {
        $notification=Notification::where('id',$id)->first();
        $notification->delete();
        
        return redirect()->route('notifications.index')
            ->with('success', 'Notification deleted');
    }
}