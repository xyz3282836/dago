<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleAction extends Model
{
    protected $fillable = ['aid', 'service_gold', 'service_rate', 'type'];
    protected $appends = ['action_desc'];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'rid');
    }

    public function action(): BelongsTo
    {
        return $this->belongsTo(Action::class, 'aid');
    }

    public function getActionDescAttribute()
    {
        return Action::where('id', $this->aid)->value('desc');
    }

}
