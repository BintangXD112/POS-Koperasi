<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_name',
        'store_address',
        'store_phone',
        'store_email',
        'store_owner',
        'store_logo',
        'store_website',
        'store_description',
        'currency',
        'timezone',
        'tax_number',
        'footer_text',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the current store settings (singleton pattern)
     */
    public static function getSettings(): self
    {
        $settings = self::first();
        
        if (!$settings) {
            $settings = self::create([
                'store_name' => 'Sistem Koperasi',
                'currency' => 'IDR',
                'timezone' => 'Asia/Jakarta',
                'is_active' => true,
            ]);
        }
        
        return $settings;
    }

    /**
     * Update store settings
     */
    public static function updateSettings(array $data): self
    {
        $settings = self::getSettings();
        $settings->update($data);
        return $settings;
    }

    /**
     * Get formatted currency
     */
    public function getFormattedCurrencyAttribute(): string
    {
        return match($this->currency) {
            'IDR' => 'Rp',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            default => $this->currency,
        };
    }

    /**
     * Get store logo URL
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->store_logo) {
            return null;
        }
        
        return asset('storage/' . $this->store_logo);
    }

    /**
     * Get store name for display
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->store_name ?: 'Sistem Koperasi';
    }

    /**
     * Get complete address
     */
    public function getCompleteAddressAttribute(): string
    {
        $parts = array_filter([
            $this->store_address,
            $this->store_phone ? "Telp: {$this->store_phone}" : null,
            $this->store_email ? "Email: {$this->store_email}" : null,
        ]);
        
        return implode("\n", $parts);
    }
}