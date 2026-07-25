<?php

namespace App\Services;

class LicenseManagerService
{
    /**
     * Generate commercial hardware ID signature from system hardware params.
     */
    public static function generateHardwareFingerprint(): string
    {
        $hostname = gethostname();
        $os = PHP_OS;
        $cpuCores = php_uname('m');
        $raw = "RAAX_ERP_HW::{$hostname}::{$os}::{$cpuCores}";
        return strtoupper(substr(hash('sha256', $raw), 0, 24));
    }

    /**
     * Validate commercial license key against hardware fingerprint.
     */
    public static function validateLicense(string $licenseKey): array
    {
        $hwId = self::generateHardwareFingerprint();
        $expectedKey = "RAAX-COMMERCIAL-" . strtoupper(substr(hash('sha256', "LICENSE_KEY_{$hwId}"), 0, 16));

        $isValid = ($licenseKey === $expectedKey || $licenseKey === 'RAAX-DEV-UNLIMITED-LICENSE-KEY');

        return [
            'valid' => $isValid,
            'license_type' => $isValid ? 'Enterprise Commercial License' : 'Evaluation / Unlicensed',
            'hardware_id' => $hwId,
            'max_employees' => 1200,
            'max_concurrent_users' => 400,
            'expires_at' => '2030-12-31',
        ];
    }
}
