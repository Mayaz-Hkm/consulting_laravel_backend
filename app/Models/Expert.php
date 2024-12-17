<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use SebastianBergmann\Type\VoidType;


class Expert extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guard = 'experts_api';
    protected $fillable = ['userName',
        'email',
        'password',
        'mobile',
        'timezone',
        'category_id',
        'section_id',
        'start_time',
        'end_time',
        'experience',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function schedules()
    {
        return $this->hasMany(ExpertSchedule::class,"expert_id");
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

}
