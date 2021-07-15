<?php

namespace Wefabric\FormCache\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wefabric\FormCache\Concerns\UsesUuid;

class FormCache extends Model
{
    use HasFactory;
    use UsesUuid;

    protected $fillable = [
        'form_data',
        'ip_address'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'form_data' => 'array',
    ];
}
