<?php

namespace App\Models\RedisModel;

use limx\utils\RedisModel\Model;

class User2 extends Model
{
    protected $key = 'redisdmodel:user:{id}';

    protected $type = 'hash';

    protected $fillable = ['id', 'username', 'name'];

    protected function initRedisClient($parameters, $options)
    {
        if (!isset($parameters['host'])) {
            $parameters['host'] = env('REDIS_HOST', 'localhost');
        }

        if (!isset($parameters['port'])) {
            $parameters['port'] = env('REDIS_PORT', 6379);
        }

        if (!isset($parameters['auth'])) {
            $parameters['password'] = env('REDIS_AUTH', null);
        }

        if (!isset($parameters['database'])) {
            $parameters['database'] = env('REDIS_INDEX', 0);
        }

        parent::initRedisClient($parameters, $options);
    }

    public function replace($primaryKey, $data)
    {
        $info = array_intersect_key($data, array_flip((array)$this->fillable));
        $data = array_merge(array_fill_keys($this->fillable, ''), $info);
        return $this->create($primaryKey, $data, 60);
    }


    public function destroy($primaryKey)
    {
        if (!is_array($primaryKey)) {
            $primaryKey = [$primaryKey];
        }

        return $this->whereIn('id', $primaryKey)->delete();
    }

    public function flushAll()
    {
        return $this->delete();
    }

}