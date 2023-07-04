<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelProject extends Model
{
    use HasFactory;

    protected $table = 'rel_projects';

    protected $fillable = [
        'project_id',
        'fo_id',
        'consenso'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}

?>