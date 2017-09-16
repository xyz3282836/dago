<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Action extends Model
{

    public function role() : BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_actions', 'aid', 'rid');
    }

}
