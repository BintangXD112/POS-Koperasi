<?php

namespace App\Http\Controllers;

use App\Models\StoreSetting;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class StoreSettingController extends Controller
{
    /**
     * Display store settings
     */
    public function index()
    {
        $settings = StoreSetting::getSettings();
        return view('admin.store-settings.index', compact('settings'));
    }

    /**
     * Update store settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'store_address' => 'nullable|string|max:1000',
            'store_phone' => 'nullable|string|max:20',
            'store_email' => 'nullable|email|max:255',
            'store_owner' => 'nullable|string|max:255',
            'store_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'store_website' => 'nullable|url|max:255',
            'store_description' => 'nullable|string|max:1000',
            'currency' => 'required|string|in:IDR,USD,EUR,GBP',
            'timezone' => 'required|string|max:50',
            'tax_number' => 'nullable|string|max:50',
            'footer_text' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ], [
            'store_name.required' => 'Nama toko wajib diisi.',
            'store_name.max' => 'Nama toko maksimal 255 karakter.',
            'store_address.max' => 'Alamat maksimal 1000 karakter.',
            'store_phone.max' => 'Nomor telepon maksimal 20 karakter.',
            'store_email.email' => 'Format email tidak valid.',
            'store_email.max' => 'Email maksimal 255 karakter.',
            'store_owner.max' => 'Nama pemilik maksimal 255 karakter.',
            'store_logo.image' => 'File harus berupa gambar.',
            'store_logo.mimes' => 'Format gambar harus jpeg, png, jpg, gif, atau svg.',
            'store_logo.max' => 'Ukuran gambar maksimal 2MB.',
            'store_website.url' => 'Format website tidak valid.',
            'store_website.max' => 'Website maksimal 255 karakter.',
            'store_description.max' => 'Deskripsi maksimal 1000 karakter.',
            'currency.required' => 'Mata uang wajib dipilih.',
            'currency.in' => 'Mata uang tidak valid.',
            'timezone.required' => 'Zona waktu wajib dipilih.',
            'timezone.max' => 'Zona waktu maksimal 50 karakter.',
            'tax_number.max' => 'Nomor pajak maksimal 50 karakter.',
            'footer_text.max' => 'Teks footer maksimal 1000 karakter.',
        ]);

        $settings = StoreSetting::getSettings();
        $data = $request->only([
            'store_name', 'store_address', 'store_phone', 'store_email',
            'store_owner', 'store_website', 'store_description', 'currency',
            'timezone', 'tax_number', 'footer_text', 'is_active'
        ]);

        // Handle logo upload
        if ($request->hasFile('store_logo')) {
            // Delete old logo if exists
            if ($settings->store_logo && Storage::disk('public')->exists($settings->store_logo)) {
                Storage::disk('public')->delete($settings->store_logo);
            }

            // Store new logo
            $logoPath = $request->file('store_logo')->store('store-logos', 'public');
            $data['store_logo'] = $logoPath;
        }

        // Update settings
        $settings->update($data);

        // Log activity
        ActivityLog::log('store_settings_update', 'Admin mengupdate pengaturan toko', [
            'updated_fields' => array_keys($data),
            'store_name' => $settings->store_name
        ]);

        return redirect()->back()->with('success', 'Pengaturan toko berhasil diperbarui!');
    }

    /**
     * Remove store logo
     */
    public function removeLogo()
    {
        $settings = StoreSetting::getSettings();
        
        if ($settings->store_logo && Storage::disk('public')->exists($settings->store_logo)) {
            Storage::disk('public')->delete($settings->store_logo);
        }
        
        $settings->update(['store_logo' => null]);

        // Log activity
        ActivityLog::log('store_logo_remove', 'Admin menghapus logo toko');

        return redirect()->back()->with('success', 'Logo toko berhasil dihapus!');
    }

    /**
     * Get store settings as JSON (for API)
     */
    public function getSettings()
    {
        $settings = StoreSetting::getSettings();
        return response()->json($settings);
    }

    /**
     * Get available timezones
     */
    public function getTimezones()
    {
        $timezones = [
            'Asia/Jakarta' => 'WIB (Jakarta)',
            'Asia/Makassar' => 'WITA (Makassar)',
            'Asia/Jayapura' => 'WIT (Jayapura)',
            'UTC' => 'UTC',
            'America/New_York' => 'EST (New York)',
            'Europe/London' => 'GMT (London)',
            'Asia/Tokyo' => 'JST (Tokyo)',
            'Asia/Shanghai' => 'CST (Shanghai)',
        ];

        return response()->json($timezones);
    }
}