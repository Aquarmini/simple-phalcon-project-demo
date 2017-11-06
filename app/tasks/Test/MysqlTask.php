<?php
// +----------------------------------------------------------------------
// | 测试脚本 [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace App\Tasks\Test;

use App\Models\Book;
use App\Models\Title;
use App\Models\User;
use App\Models\UserTitle;
use App\Utils\DB;
use limx\Support\Str;
use Phalcon\Cli\Task;
use Xin\Cli\Color;
use App\Logics\Test;

class MysqlTask extends Task
{
    public function mainAction()
    {
        echo Color::head('Help:') . PHP_EOL;
        echo Color::colorize('  PHP函数参数测试') . PHP_EOL . PHP_EOL;

        echo Color::head('Usage:') . PHP_EOL;
        echo Color::colorize('  php run Test\\\\Arg [action]', Color::FG_GREEN) . PHP_EOL . PHP_EOL;

        echo Color::head('Actions:') . PHP_EOL;
        echo Color::colorize('  inc             新建记录如果重复则修改', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  modelSave       通过模型新建数据', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  modelUpdate     通过模型更新数据', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  modelUpdateNoIndex     通过模型更新没有主键的数据', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  modelWrite      写入', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  modelUpdateOnly 只写入某些字段', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  testIn          测试In的效率', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  builder         测试builder', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  relation        测试relation', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  eager           测试eager', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  dynamic         测试dynamic更新', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  modelAdd        通过模型实现++', Color::FG_GREEN) . PHP_EOL;

    }

    public function modelAddAction()
    {
        $user = User::findFirst([
            'order' => 'id DESC'
        ]);

        $user_id = $user->id;
        $sql = "UPDATE `user` SET `role_id` = role_id + 1 WHERE `id` = ?";
        DB::execute($sql, [$user_id]);

        $user->role_id = new \Phalcon\Db\RawValue('role_id + 1');
        $user->save();
    }

    public function dynamicAction()
    {
        $user = User::findFirst([
            'order' => 'id DESC'
        ]);
        $user->name = time();
        $user->save();
    }

    public function eagerAction()
    {
        $user = User::with(['book.user.book', 'title'], [
            'conditions' => 'id IN (1,2)'
        ]);
        foreach ($user as $v) {
            foreach ($v->book as $b) {
                dd($b->user->toArray());
            }
            foreach ($v->book as $b) {
                dd($b->toArray());
            }
            foreach ($v->title as $b) {
                dd($b->toArray());
            }
        }
    }

    public function relationAction()
    {
        $user = User::findFirst(1);
        $user->book;

        $user = User::findFirst(1);
        $user->book;
        dd(1);

        $books = Book::find([
            'conditions' => 'id IN (?0,?1,?2,?3,?4)',
            'bind' => [14, 20, 23, 25, 5]
        ]);

        foreach ($books as $v) {
            $t = $v->user;
        }

        dd($books->toArray());
    }

    public function builderAction()
    {
        $res = $this->modelsManager->createBuilder()
            ->addFrom(User::class, 'u')
            // ->columns('u.id,ut.uid,t.name')
            ->leftJoin(UserTitle::class, "u.id = ut.uid", 'ut')
            ->leftJoin(Title::class, 'ut.title_id = t.id', 't')
            ->where("u.id = ?0")
            ->getQuery()
            ->execute([1]);

        $res2 = $this->modelsManager->createBuilder()->from(User::class)
            ->inWhere('id', [1, 2])
            ->getQuery()
            ->execute()
            ->toArray();

        dd($res2);

        foreach ($res as $item) {
            dd($item);
            dd($item->user_title->toArray());
        }
    }

    public function modelListAction()
    {
        $ids = [];
        for ($i = 0; $i < 5000; $i++) {
            $ids[] = rand(0, 327310);
        }
        $sql = "SELECT id,user_login FROM test_sphinx WHERE id IN (" . implode(',', $ids) . ")";
        $time = microtime(true);
        DB::query($sql);
        echo Color::colorize("Query 50000 By In Time=" . (microtime(true) - $time), Color::FG_GREEN) . PHP_EOL;

        $str = '';
        foreach ($ids as $id) {
            $str .= ' id = ' . $id . ' OR';
        }
        $where = rtrim($str, 'OR');
        $sql = "SELECT id,user_login FROM test_sphinx WHERE " . $where;
        $time = microtime(true);
        DB::query($sql);

        echo Color::colorize("Query 50000 By OR Time=" . (microtime(true) - $time), Color::FG_GREEN) . PHP_EOL;
    }

    public function modelUpdateOnlyAction()
    {
        $user = User::findFirst([
            'order' => 'id DESC'
        ]);
        $res = $user->updateOnly([
            'username' => uniqid(),
        ]);

        $res = $user->updateOnly([
            'name' => uniqid(),
        ]);
        print_r($res);
    }

    public function modelWriteAction()
    {
        $user = User::findFirst(1);
        $user->writeAttribute('email', '715557344@q.com');
        $user->save();
    }

    public function modelUpdateNoIndexAction()
    {
        $res = UserTitle::findFirst([
            'conditions' => 'uid=?0 AND title_id=?1',
            'bind' => [1, 8],
        ]);
        print_r($res->toArray());
        $res->title_id = 4;
        $res->save();
    }

    public function modelSaveAction()
    {
        $user = new \App\Models\User();
        $user->username = Str::quickRandom(12);
        $user->name = '测试';
        $user->password = md5(910123);
        $user->role_id = 1;
        $res = $user->save();
        print_r($res);
    }

    public function modelUpdateAction()
    {
        $user = \App\Models\User::findFirst(24);
        $user->username = Str::quickRandom(12);
        $res = $user->save();
        print_r($res);

        $user = \App\Models\User::findFirst([
            'conditions' => 'id=?0',
            'bind' => [24],
        ]);
        $user->username = Str::quickRandom(12);
        $res = $user->save();
        print_r($res);

        $res = $user->updateOnly([
            'username' => 'only update',
        ]);
        print_r($res);

    }

    public function incAction()
    {
        $res = (new Test())->incrSql();
        dump($res);
    }

}