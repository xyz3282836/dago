<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Action extends Model
{

    public function role() : BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_actions', 'aid', 'rid');
    }

    public function actiondesc(): hasMany
    {
        return $this->hasMany(RoleAction::class, 'aid');
    }

    public function getEpageTable(){
        $list = $this->with('actiondesc')->where('name','euploadimg')->orWhere('name','euploadvideo')->get()->toArray();

        return $list;
    }

}
