<?php

namespace Aura\Notifications\Http\Controllers;

use Aura\Notifications\Models\SystemUpdate;
use Aura\Notifications\Services\SystemUpdateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class SystemUpdateController extends Controller
{
    public function __construct(
        protected SystemUpdateService $service
    ) {}

    /**
     * Display a listing of system updates.
     */
    public function index(Request $request): View
    {
        $userId = auth()->id();
        $teamId = auth()->user()->current_team_id ?? null;

        $updates = $this->service->getAllUpdates($userId, $teamId);

        return view('aura-notifications::updates.index', [
            'updates' => $updates,
            'unreadCount' => $this->service->getUnreadCount($userId, $teamId),
        ]);
    }

    /**
     * Display a specific system update.
     */
    public function show(string $slug): View
    {
        $update = SystemUpdate::where('slug', $slug)
            ->published()
            ->firstOrFail();

        $userId = auth()->id();

        // Mark as read when viewing
        $this->service->markAsRead($update, $userId);

        return view('aura-notifications::updates.show', [
            'update' => $update,
        ]);
    }

    /**
     * Mark a system update as read.
     */
    public function markAsRead(Request $request, int $id): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $update = SystemUpdate::findOrFail($id);
        $this->service->markAsRead($update, auth()->id());

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', __('Update marked as read.'));
    }
}
