<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Foodgroup extends Model
{
    use HasFactory;

    protected $table = 'foodgroups';

    public function foods()
    {
        return $this->hasMany(Food::class);
    }
}
