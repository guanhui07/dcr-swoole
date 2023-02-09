<?php

declare(strict_types=1);

namespace database\migrations;

use App\Model\UserModel;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Illuminate\Database\Schema\Blueprint;
use guanhui07\SwooleDatabase\Adapter\Manager as DB;

//use Illuminate\Support\Facades\DB; // 不能用laravel的门面

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230102054754 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        go(function () {
//            $test = DB::select('select 23');
//            var_dump($test);
//            $test = UserModel::query()->limit(1)->get(['id', 'name']);
//            print_r($test->toArray());

            $sql = "
            ALTER TABLE `user` comment'user表 test'
            ";
            \Guanhui07\SwooleDatabase\Adapter\DB::statement($sql);


            /**
            function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_no',30)->default('')->nullable(false)->comment('订单号');
            $table->string('store_no',15)->default('')->nullable(false)->comment('门店编号');
            $table->decimal('amount',10)->default("0.00")->nullable(false)->comment('总金额');
            $table->integer('pay_time')->default(0)->nullable(false)->comment('支付时间');
            $table->tinyInteger('type')->default(1)->nullable(false)->comment('类型 退货/支付等');
            $table->integer('buyer_id')->default(0)->nullable(false)->comment('购买人用户ID');
            $table->integer('line_type')->default(1)->nullable(false)->comment('1线上2线下');
            $table->string('pay_no')->nullable(true)->comment('type 为2，退货单时的 付款单号');


            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            // 创建索引
            $table->index(['order_no','buyer_id'], 'order_no_buyer_id');
            $table->index('buyer_id','buyer_id');

            $table->dateTime('created_at')->comment('创建时间');
            $table->dateTime('updated_at')->comment('更新时间');
            }
             */
//            (new \Doctrine\DBAL\Schema\Schema)
//                ->createTable('test')
//            ;
        });
        // @see https://github.com/swoole/swoole-src/issues/4552
        \Swoole\Event::wait();


    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
