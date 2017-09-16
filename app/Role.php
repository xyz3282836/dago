<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{

    public function action(): BelongsToMany
    {
        return $this->belongsToMany(Action::class, 'role_actions', 'rid', 'aid');
    }

    public function actiondesc(): hasMany
    {
        return $this->hasMany(RoleAction::class, 'rid');
    }

}
