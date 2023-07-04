<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

    public function relProjects()
    {
        return $this->hasMany(RelProject::class);
    }

    public function relUsers()
    {
        return $this->hasMany(RelUser::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'rel_users', 'project_id', 'user_id')->withPivot('consistencia');
    }

    public function fo()
    {
        return $this->hasMany(FO::class, 'rel_projects');
    }
}
