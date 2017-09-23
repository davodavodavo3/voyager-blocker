<?php

namespace VoyagerBlocker\Models;

use Illuminate\Database\Eloquent\Model;

class VoyagerBlocker extends Model
{
    protected $table = 'voyager_blocker';
    protected $fillable = ['ips'];

}
