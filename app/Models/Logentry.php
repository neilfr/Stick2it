<?php

namespace App\Models;

use App\Models\Food;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Logentry extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'food_id',
        'quantity',
        'consumed_at',
    ];

    protected $table = 'logentries';

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function food(){
        return $this->belongsTo(Food::class)->withTrashed();
    }

    public function scopeUserLogentries(Builder $query)
    {
        $query->where('user_id', auth()->user()->id)
        ->orderBy('consumed_at', 'DESC');
    }

    public function scopeInDateRange(Builder $query, $from, $to)
    {
        if (is_null($from) && is_null($to)){
            return $query->where('consumed_at', '>=', now()->subDays(7)->toDateString())
                ->where('consumed_at', '<=', Carbon::now()->toDateString());
        }

        if (is_null($from)){
            return $query->whereDate('consumed_at', '<=', $to);
        }

        if (is_null($to)){
            return $query->whereDate('consumed_at', '>=', $from);
        }

        $query->whereBetween('consumed_at', [$from, $to]);
    }
}
