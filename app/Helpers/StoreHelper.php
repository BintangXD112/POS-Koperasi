<?php

namespace App\Helpers;

use App\Models\StoreSetting;

class StoreHelper
{
    /**
     * Get store settings
     */
    public static function getStoreSettings(): StoreSetting
    {
        return StoreSetting::getSettings();
    }

    /**
     * Get store name
     */
    public static function getStoreName(): string
    {
        return self::getStoreSettings()->display_name;
    }

    /**
     * Get store logo URL
     */
    public static function getStoreLogoUrl(): ?string
    {
        return self::getStoreSettings()->logo_url;
    }

    /**
     * Get store address
     */
    public static function getStoreAddress(): ?string
    {
        return self::getStoreSettings()->store_address;
    }

    /**
     * Get store phone
     */
    public static function getStorePhone(): ?string
    {
        return self::getStoreSettings()->store_phone;
    }

    /**
     * Get store email
     */
    public static function getStoreEmail(): ?string
    {
        return self::getStoreSettings()->store_email;
    }

    /**
     * Get formatted currency
     */
    public static function getFormattedCurrency(): string
    {
        return self::getStoreSettings()->formatted_currency;
    }

    /**
     * Get complete store info
     */
    public static function getCompleteStoreInfo(): array
    {
        $settings = self::getStoreSettings();
        
        return [
            'name' => $settings->display_name,
            'logo_url' => $settings->logo_url,
            'address' => $settings->store_address,
            'phone' => $settings->store_phone,
            'email' => $settings->store_email,
            'owner' => $settings->store_owner,
            'website' => $settings->store_website,
            'description' => $settings->store_description,
            'currency' => $settings->formatted_currency,
            'timezone' => $settings->timezone,
            'tax_number' => $settings->tax_number,
            'footer_text' => $settings->footer_text,
            'is_active' => $settings->is_active,
        ];
    }
}
