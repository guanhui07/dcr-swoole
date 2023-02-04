<?php

declare(strict_types=1);

namespace DcrSwoole\Command;

use App\Console\Kernel;
use Exception;
use Inhere\Console\Application;
use Inhere\Console\IO\Input;
use Inhere\Console\IO\Output;

/**
 * 命令行应用 初始化 绑定命令类
 * Class CommandBootstrap
 * @package dcr
 * @see https://github.com/inhere/php-console/wiki
 */
class CommandBootstrap
{
    public static function initApplication(array $meta, Input $input, Output $output): Application
    {
        return new Application($meta, $input, $output);
    }

    /**
     * @see https://github.com/inhere/php-console/wiki
     */
    public static function bootstrap(): void
    {
        $meta = [
            'name' => 'My Console App',
            'version' => '1.0.2',
        ];
        $input = new Input();
        $output = new Output();
        // 通常无需传入 $input $output ，会自动创建
        $app = self::initApplication($meta, $input, $output);
        $commands = Kernel::getCommands();
        $app->addCommands($commands);

//        $configs = Kernel::getCommands();
//        foreach ($configs as $class) {
//            $app->command($class);
//        }

        try {
            $app->run();
        } catch (Exception $e) {
            echo $e->getMessage();
            // handle the exception
        }
    }
}
