<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrgSection extends Model
{
    protected $fillable = [
        'name',
        'display_mode',
        'table_columns',
        'order',
    ];

    protected $casts = [
        'table_columns' => 'array',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
