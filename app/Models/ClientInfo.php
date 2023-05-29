<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientInfo extends Model
{
    use HasFactory;
    protected $fillable = [
        "company_user_name",
        "company_address",
        "company_phone",
        " company_email",
        "company_website",
        "company_logo",
        "company_country",
        "company_user_name",
        "company_user_email",
        "company_user_phone",
        "company_user_gender",
        "company_user_image"

    ];
}
