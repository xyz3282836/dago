<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Log;

class User extends Authenticatable
{
    use Notifiable;

    const TYPE_REGULAR = 1;
    const TYPE_VIP     = 2;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'shop_id', 'password', 'mobile', 'addr', 'management_type', 'shipping_addr', 'real_name', 'idcardpic', 'idcardno', 'golds', 'lock_golds', 'balance', 'lock_balance', 'last_login_time', 'is_evaluate'
    ];
    protected $appends = ['level_text'];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getLevelTextAttribute()
    {
        return $this->role->desc;
    }

    public function getEvaluateAttribute()
    {
        $arr = config('linepro.user_evaluate');
        return $arr[$this->is_evaluate];
    }

    public function checkInfoIsCompleted()
    {
        if ($this->mobile == '') {
            return false;
        }
        if ($this->shipping_addr == '') {
            return false;
        }
        if ($this->real_name == '') {
            return false;
        }
        if ($this->idcardno == '') {
            return false;
        }
        if ($this->idcardpic == '') {
            return false;
        }
        return true;
    }

    public function scopeType($query, $type)
    {
        if ($type == 'all') {
            return $query;
        }
        return $query->where('level', $type);
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'level');
    }

    public function getEpageTable()
    {
        $ra  = $this->role->actiondesc;
        $i   = 0;
        $arr = [];
        foreach ($ra as $v) {
            if (in_array($v->action->name, ['evaluate', 'euploadpic', 'euploadvideo'])) {
                $i++;
                $arr[] = [
                    'desc'    => $v->action->desc,
                    'gold'    => $v->service_gold,
                    'num'     => 0,
                    'allgold' => 0
                ];
            }
            if ($i == 3) {
                break;
            }
        }
        return json_encode($arr);
    }

    public function getConsumeGold($payarr, $model = null)
    {
        $allgold = 0;
        $arr     = [];
        foreach ($this->role->actiondesc as $v) {
            $arr[$v->action->name] = [
                'gold' => $v->service_gold,
                'rate' => $v->service_rate,
                'type' => $v->type
            ];
        }
        $rarr = [];
        foreach ($payarr as $k => $v) {
            if (!isset($arr[$k])) {
                Log::error('用户id' . $this->id . '没有评价' . $k . '权限');
                return false;
            }
            if ($arr[$k]['type'] == 1) {
                $allgold += $v * $arr[$k]['gold'];
            }
            $rarr[$k] = [
                'num'  => $v,
                'gold' => $v * $arr[$k]['gold']
            ];
        }
        return [$allgold, $rarr];
    }

    public function checkAction($action)
    {
        $actions = [];
        foreach ($this->role->actiondesc as $v) {
            $actions[] = $v->action->name;
        }
        if (!in_array(strtolower($action), $actions)) {
            return false;
        }
        return true;
    }

    public function getActionGold($action)
    {
        $aid = Action::where('name', $action)->value('id');
        $ra  = RoleAction::where('rid', $this->level)->where('aid', $aid)->first();
        if (!$ra) {
            return 0;
        }
        return $ra->service_gold;
    }
}
