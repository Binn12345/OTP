<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OtpLog extends Model
{
    protected $fillable = ['user_id', 'otp', 'status','email','ip_address','user_agent'];
}
