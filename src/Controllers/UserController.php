<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Services\Cache\RedisCache;

class UserController
{
    public function index()
    {
        $cache = new RedisCache();
        $cacheKey = 'users.all';

        $data = $cache->get($cacheKey);
        if ($data) {
            return json_response(['source' => 'redis', 'data' => $data]);
        }

        // مثال: نقرأ من كلا القاعدتين للتوضيح
        $modelMysql = new UserModel('mysql');
        $modelMaria = new UserModel('mariadb');

        $usersMysql = $modelMysql->all();
        $usersMaria = $modelMaria->all();

        $result = [
            'mysql' => $usersMysql,
            'mariadb' => $usersMaria
        ];

        $cache->set($cacheKey, $result, 60); // cache 60s
        return json_response(['source' => 'db', 'data' => $result]);
    }

    public function show($id)
    {
        $model = new UserModel('mysql');
        $user = $model->find($id);
        if (!$user) return json_response(['error' => 'not found'], 404);
        return json_response($user);
    }

    public function store()
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        if (empty($input['name']) || empty($input['email'])) {
            return json_response(['error' => 'name and email required'], 422);
        }
        $model = new UserModel('mysql');
        $id = $model->create($input['name'], $input['email']);
        return json_response(['id' => $id], 201);
    }
}
