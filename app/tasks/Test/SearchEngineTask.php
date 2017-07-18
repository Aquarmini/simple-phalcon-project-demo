<?php

namespace App\Tasks\Test;

use limx\phalcon\Cli\Color;

class SearchEngineTask extends \Phalcon\Cli\Task
{
    public function onConstruct()
    {
        define('XS_APP_ROOT', ROOT_PATH . '/data/xunsearch/');
    }

    public function mainAction()
    {
        echo Color::head('Help:') . PHP_EOL;
        echo Color::colorize('  开源搜索引擎测试') . PHP_EOL . PHP_EOL;

        echo Color::head('Usage:') . PHP_EOL;
        echo Color::colorize('  php run Test\\\\SearchEngine [action]', Color::FG_GREEN) . PHP_EOL . PHP_EOL;

        echo Color::head('Actions:') . PHP_EOL;
        echo Color::colorize('  xsAdd       讯搜添加文档', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  xsQuery     讯搜搜索文档', Color::FG_GREEN) . PHP_EOL;
    }

    public function xsAddAction()
    {
        $xs = new \XS('demo'); // 建立 XS 对象，项目名称为：demo
        $index = $xs->index; // 获取 索引对象
        // 执行清空操作
        // $index->clean();
        $index->beginRebuild();

        $step = 100;
        $begin = 0;
        $users = [1];
        while (count($users) > 0 && $begin < 1000) {
            $users = \App\Models\TestSphinx::find([
                'offset' => $begin,
                'limit' => $step,
                'columns' => 'id,user_login,user_nicename,create_time,avatar',
            ]);
            $begin += $step;
            echo Color::colorize("已导入" . $begin . "数据！", Color::FG_GREEN) . PHP_EOL;

            foreach ($users as $user) {
                $data = $user->toArray();
                // 创建文档对象
                $doc = new \XSDocument;
                $doc->setFields($data);

                // 添加到索引数据库中
                // $index->add($doc);
                $index->update($doc);
            }
        }
        echo Color::colorize("导入完毕", Color::FG_LIGHT_CYAN) . PHP_EOL;
        $index->endRebuild();
    }

    public function xsQueryAction()
    {
        $xs = new \XS('demo'); // 建立 XS 对象，项目名称为：demo
        $search = $xs->search; // 获取 搜索对象

        $query = '超级'; // 这里的搜索语句很简单，就一个短语

        $search->setQuery($query); // 设置搜索语句
        // $search->addWeight('subject', 'xunsearch'); // 增加附加条件：提升标题中包含 'xunsearch' 的记录的权重
        // $search->setLimit(5, 0); // 设置返回结果最多为 5 条，并跳过前 10 条

        $docs = $search->search(); // 执行搜索，将搜索结果文档保存在 $docs 数组中
        $count = $search->count(); // 获取搜索结果的匹配总数估算值
        print_r($docs);
        echo Color::colorize("共搜索到" . $count . "个文档", Color::FG_LIGHT_CYAN) . PHP_EOL;
    }

}

