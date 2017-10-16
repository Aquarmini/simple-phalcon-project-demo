<?php

namespace App\Tasks\Test;

use App\Tasks\Task;
use Phalcon\Mvc\View\Engine\Volt;
use Xin\Cli\Color;

class BuilderTask extends Task
{
    public function mainAction()
    {
        echo Color::head('Help:') . PHP_EOL;
        echo Color::colorize('  模板文件创建测试') . PHP_EOL . PHP_EOL;

        echo Color::head('Usage:') . PHP_EOL;
        echo Color::colorize('  php run test:builder@[action]', Color::FG_GREEN) . PHP_EOL . PHP_EOL;

        echo Color::head('Actions:') . PHP_EOL;
        echo Color::colorize('  volt                   Volt模板引擎', Color::FG_GREEN), PHP_EOL;
    }

    public function voltAction()
    {
        /** @var \Phalcon\Mvc\View $view */
        $view = di('view');
        $config = di('config');

        $data = [
            'user' => 'limx',
            'books' => [
                'Fire',
                'PHP'
            ],
        ];
        $view->start();
        $view->setVars($data);
        $view->render("templates", "user");
        $view->finish();
        $content = $view->getContent();

        $path = ROOT_PATH . '/storage/output/' . 'user.html';
        file_put_contents($path, $content);
        echo Color::success("文件生成成功");
    }

}

