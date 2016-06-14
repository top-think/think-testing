<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2015 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------

namespace think\testing\command;


use PHPUnit_TextUI_Command;
use think\console\command\Command;
use think\console\Input;
use think\console\Output;
use think\Loader;
use think\Session;

class Test extends Command
{
    public function configure()
    {
        $this->setName('test')->setDescription('phpunit');
    }

    public function execute(Input $input, Output $output)
    {
        Loader::addMap('TestCase', ROOT_PATH . 'tests/TestCase.php');
        Loader::addMap('think\App', CORE_PATH . 'App' . EXT);

        Session::init();
        (new PHPUnit_TextUI_Command())->run(['phpunit']);
    }
}