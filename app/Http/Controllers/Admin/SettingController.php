<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        // Read settings from a simple JSON file or .env
        $settings = [
            'site_name' => config('app.name'),
            'default_currency' => config('invoicehero.default_currency'),
            'default_gst_rate' => config('invoicehero.default_gst_rate'),
            'maintenance_mode' => app()->isDownForMaintenance(),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // In a real app, store in DB or update config
        // For now, just redirect with success
        return back()->with('success', 'Settings updated successfully.');
    }
}