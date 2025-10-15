<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function edit()
    {
        $settings = Setting::all()->keyBy('key')->map(fn ($item) => $item->value)->toArray();
        
        return view('admin.settings.edit', compact('settings'));
    }


    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_address' => 'required|string|max:500',
            'site_email' => 'required|email|max:255',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);
        
        foreach ($request->except(['_token', '_method', 'site_logo']) as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        if ($request->hasFile('site_logo')) {
            $oldLogo = Setting::where('key', 'site_logo')->first();
            if ($oldLogo && $oldLogo->value) {
                 Storage::disk('public')->delete($oldLogo->value);
            }
            
            $logoPath = $request->file('site_logo')->store('site', 'public');
            Setting::updateOrCreate(
                ['key' => 'site_logo'],
                ['value' => $logoPath]
            );
        }

        return redirect()->back()->with('success', 'Pengaturan website berhasil diperbarui!');
    }
}