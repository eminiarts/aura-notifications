<?php

namespace Aura\Notifications\Http\Controllers;

use Aura\Notifications\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationService $service
    ) {}

    /**
     * Display a listing of user notifications.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $filter = $request->get('filter', 'all');

        $notifications = $this->service->getPaginated($user, 20, $filter);

        return view('aura-notifications::notifications.index', [
            'notifications' => $notifications,
            'filter' => $filter,
            'unreadCount' => $this->service->getUnreadCount($user),
            'statistics' => $this->service->getStatistics($user),
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $this->service->markAsRead($notification);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', __('Notification marked as read.'));
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request): JsonResponse|RedirectResponse
    {
        $this->service->markAllAsRead(auth()->user());

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', __('All notifications marked as read.'));
    }

    /**
     * Delete a notification.
     */
    public function destroy(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $this->service->archive($notification);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', __('Notification deleted.'));
    }
}
