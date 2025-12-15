<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Models\UserModel;
use App\Services\Cache\RedisCache;

class UserController
{
    /**
     * عرض جميع المستخدمين مع كاش Redis
     */
    public function index(Request $request): void
    {
        $cache = new RedisCache();
        $cacheKey = 'users.all';

        $data = $cache->get($cacheKey);
        if ($data) {
            Response::json(['source' => 'redis', 'data' => $data]);
            return;
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
        Response::json(['source' => 'db', 'data' => $result]);
    }

    /**
     * عرض مستخدم واحد
     */
    public function show(Request $request, $id): void
    {
        $model = new UserModel('mysql');
        $user = $model->find($id);
        if (!$user) {
            Response::json(['error' => 'not found'], 404);
            return;
        }
        Response::json($user);
    }

    /**
     * إنشاء مستخدم جديد
     */
    public function store(Request $request): void
    {
        $input = $request->body;
        if (empty($input['name']) || empty($input['email'])) {
            Response::json(['error' => 'name and email required'], 422);
            return;
        }
        if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            Response::json(['error' => 'invalid email'], 422);
            return;
        }

        $model = new UserModel('mysql');
        $id = $model->create($input['name'], $input['email']);
        Response::json(['id' => $id], 201);
    }

    /**
     * تحديث مستخدم
     */
    public function update(Request $request, $id): void
    {
        $input = $request->body;
        if (empty($input['name']) && empty($input['email'])) {
            Response::json(['error' => 'name or email required'], 422);
            return;
        }
        if (!empty($input['email']) && !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            Response::json(['error' => 'invalid email'], 422);
            return;
        }

        $model = new UserModel('mysql');
        $user = $model->find($id);
        if (!$user) {
            Response::json(['error' => 'not found'], 404);
            return;
        }
        $model->update($id, $input['name'] ?? $user['name'], $input['email'] ?? $user['email']);
        Response::json(['status' => 'updated']);
    }

    /**
     * حذف مستخدم
     */
    public function destroy(Request $request, $id): void
    {
        $model = new UserModel('mysql');
        $user = $model->find($id);
        if (!$user) {
            Response::json(['error' => 'not found'], 404);
            return;
        }
        $model->delete($id);
        Response::json(['status' => 'deleted']);
    }
}
