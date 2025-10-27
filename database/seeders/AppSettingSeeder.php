<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AppSetting;

class AppSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Performance Settings
        AppSetting::setValue(
            'lazy_loading',
            true,
            'boolean',
            'Enable/disable lazy loading for better user experience'
        );

        // Theme Settings
        AppSetting::setValue(
            'dark_mode',
            false,
            'boolean',
            'Enable dark mode theme for better viewing experience'
        );

        // Notification Settings
        AppSetting::setValue(
            'email_notifications',
            true,
            'boolean',
            'Enable email notifications for transactions and updates'
        );

        AppSetting::setValue(
            'browser_notifications',
            true,
            'boolean',
            'Enable browser push notifications'
        );

        // Auto Export Settings
        AppSetting::setValue(
            'auto_export_enabled',
            false,
            'boolean',
            'Enable automatic export of reports'
        );

        AppSetting::setValue(
            'auto_export_schedule',
            'daily',
            'string',
            'Auto export schedule: daily, weekly, monthly'
        );

        AppSetting::setValue(
            'auto_export_format',
            'excel',
            'string',
            'Default export format: excel, pdf'
        );

        // Data Cleanup Settings
        AppSetting::setValue(
            'data_cleanup_enabled',
            false,
            'boolean',
            'Enable automatic cleanup of old data'
        );

        AppSetting::setValue(
            'data_retention_days',
            '365',
            'integer',
            'Number of days to retain data before cleanup'
        );
    }
}
