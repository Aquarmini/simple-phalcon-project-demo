<?php

namespace App\Models\RedisModel;

class User extends BaseModel
{
    protected $key = 'redisdmodel:user:{id}';

    protected $type = 'hash';

    protected $fillable = ['id', 'username', 'name'];

}