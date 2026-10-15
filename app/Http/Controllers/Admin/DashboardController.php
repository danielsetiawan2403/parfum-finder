<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Perfume;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'perfumeCount' => Perfume::count(),
            'userCount'    => User::where('role', '!=', 'admin')->count(),
            'premiumCount' => PremiumMemberController::activeCount(),
            'pendingChats' => ChatController::pendingCount(),
        ]);
    }
}
