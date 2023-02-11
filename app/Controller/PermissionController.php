<?php
declare(strict_types=1);


namespace App\Controller;


use DcrSwoole\Annotation\Mapping\RequestMapping;
use DcrSwoole\Permission\Permission;

class PermissionController extends Controller
{
    /**
     * MqController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://github.com/php-casbin/php-casbin
     * @return array
     */
    #[RequestMapping(methods: "GET", path: "/permission/test")]
    public function test()
    {
        Permission::addPermissionForUser('eve', 'articles', 'read');
        // adds a role for a user.
        Permission::addRoleForUser('eve', 'writer');
        // adds permissions to a rule
        Permission::addPolicy('writer', 'articles', 'edit');
        return ['msg'=>'添加成功'];
    }

    #[RequestMapping(methods: "GET", path: "/permission/test2")]
    public function test2()
    {
        if (Permission::enforce("eve", "articles", "edit")) {
            $str = '恭喜你！通过权限认证';
        } else {
            $str = '对不起，您没有该资源访问权限';
        }
        echo $str;
        return ['msg'=>$str];
    }
}