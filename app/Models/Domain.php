<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
Use \Carbon\Carbon;

class Domain extends Model
{
    use HasFactory;
    Const domainType = ['curency'=> 1];
    Const domainStatus = ['active'=> 1 , 'deactive'=> 2];
}
