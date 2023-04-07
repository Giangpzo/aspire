<?php

namespace App\Modules\Auth\Transformers;

use App\Modules\Auth\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        return [
            'id' => data_get($user, 'id'),
            'name' => data_get($user, 'name'),
            'email' => data_get($user, 'email'),
            'type' => data_get($user, 'type')
        ];
    }
}