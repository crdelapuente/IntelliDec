<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pref extends Model
{
    use HasFactory;

    // Tabla asociada
    protected $table = 'prefs';

    // Campos asignables masivamente
    protected $fillable = ['rel_id', 'ci', 'cj', 'pref'];

    // La relación con la tabla rel_users
    public function relUser()
    {
        return $this->belongsTo(RelUser::class, 'rel_id');
    }
}

?>