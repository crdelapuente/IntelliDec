<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelUser extends Model
{
    use HasFactory;

    protected $table = 'rel_users';

    protected $fillable = [
        'project_id',
        'user_id',
        'consistencia'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pref()
    {
        return $this->hasMany(Pref::class, 'rel_id');
    }

}
