<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\SendNotifications;
use App\Models\Group;
use App\Models\Notification;
use App\Models\NotificationStatus;
use App\User;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function index()
    {
        $this->authorize('admin_notifications_list');

        $notifications = Notification::where('user_id', 1)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('admin/pages/users.notifications'),
            'notifications' => $notifications
        ];

        return view('admin.notifications.lists', $data);
    }

    public function create()
    {
        $this->authorize('admin_notifications_send');

        $userGroups = Group::all();

        $data = [
            'pageTitle' => trans('notification.send_notification'),
            'userGroups' => $userGroups
        ];

        return view('admin.notifications.send', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_notifications_send');

        $this->validate($request, [
            'title' => 'required|string',
            'type' => 'required|string',
            'user_id' => 'required_if:type,single',
            'group_id' => 'required_if:type,group',
            'message' => 'required|string',
        ]);

        $data = $request->all();

        Notification::create([
            'user_id' => !empty($data['user_id']) ? $data['user_id'] : null,
            'group_id' => !empty($data['group_id']) ? $data['group_id'] : null,
            'title' => $data['title'],
            'message' => $data['message'],
            'sender' => auth()->user()->full_name,
            'type' => $data['type'],
            'created_at' => time()
        ]);

        if (!empty($data['user_id'])) {
            $user = \App\User::where('id', $data['user_id'])->first();
            if (!empty($user) and !empty($user->email)) {
                \Mail::to($user->email)->send(new SendNotifications(['title' => $data['title'], 'message' => $data['message']]));
            }
        }


        return redirect('/admin/notifications');
    }

    public function edit($id)
    {
        $this->authorize('admin_notifications_edit');

        $notification = Notification::where('id', $id)
            ->with(['user' => function ($query) {
                $query->select('id', 'full_name');
            }, 'group'])->first();

        if (!empty($notification)) {
            $userGroups = Group::all();

            $data = [
                'pageTitle' => trans('notification.edit_notification'),
                'userGroups' => $userGroups,
                'notification' => $notification
            ];

            return view('admin.notifications.send', $data);
        }

        abort(404);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_notifications_edit');

        $this->validate($request, [
            'title' => 'required|string',
            'type' => 'required|string',
            'user_id' => 'required_if:type,single',
            'group_id' => 'required_if:type,group',
            'message' => 'required|string',
        ]);

        $data = $request->all();

        $notification = Notification::findOrFail($id);

        $notification->update([
            'user_id' => !empty($data['user_id']) ? $data['user_id'] : null,
            'group_id' => !empty($data['group_id']) ? $data['group_id'] : null,
            'title' => $data['title'],
            'message' => $data['message'],
            'sender' => auth()->user()->full_name,
            'type' => $data['type'],
            'created_at' => time()
        ]);

        return redirect('/admin/notifications');
    }

    public function delete($id)
    {
        $this->authorize('admin_notifications_delete');

        $notification = Notification::findOrFail($id);

        $notification->delete();

        return redirect('/admin/notifications');
    }

    public function markAllRead()
    {
        $this->authorize('admin_notifications_markAllRead');

        $adminUser = User::find(1);

        $unreadNotifications = $adminUser->getUnReadNotifications();

        if (!empty($unreadNotifications) and !$unreadNotifications->isEmpty()) {
            foreach ($unreadNotifications as $unreadNotification) {
                NotificationStatus::updateOrCreate(
                    [
                        'user_id' => $adminUser->id,
                        'notification_id' => $unreadNotification->id,
                    ],
                    [
                        'seen_at' => time()
                    ]
                );
            }
        }

        return back();
    }

    public function markAsRead($id)
    {
        $this->authorize('admin_notifications_edit');

        $adminUser = User::find(1);

        NotificationStatus::updateOrCreate(
            [
                'user_id' => $adminUser->id,
                'notification_id' => $id,
            ],
            [
                'seen_at' => time()
            ]
        );


        return response()->json([], 200);
    }
}
