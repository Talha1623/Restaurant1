<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Rider extends Model
{
    use HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',               // Password for authentication
        'dob',                    // Date of Birth
        'ni_number',              // National Insurance Number
        'cnic',                   // Pakistan version (optional, can remove if not needed)
        'city',
        'house_number',           // House Number
        'street',                 // Street
        'building',               // Building
        'address',                // Keep for backward compatibility
        'postcode',               // UK Postcode
        'vehicle_type',
        'vehicle_number',
        'license_number',
        'license_front_image',    // License Front Image
        'license_back_image',     // License Back Image
        'insurance_doc',          // Insurance document file path
        'mot_doc',                // MOT certificate file path
        'right_to_work_doc',      // Right to work doc file path
        'features',               // Notes
        'status',
        'blocked',                // Blocked status
        'joining_date',
        'bank_sort_code',         // UK Bank Sort Code
        'bank_account_number',    // UK Bank Account Number
        'photo',                  // Profile photo
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'dob' => 'date',
        'joining_date' => 'date',
    ];
}
