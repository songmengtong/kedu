<?php

/**
 *  
 * @file   Index.php  
 * @date   2016-8-23 16:03:10 
 * @author Zhenxun Du<5552123@qq.com>  
 * @version    SVN:$Id:$ 
 */  

namespace app\admin\controller;

use app\admin\model\AdminUser;
use think\Db;

class Index extends Base{
    /**
     * 后台首页
     */
    public function index(){

        $yesterday = strtotime(date('Y-m-d',strtotime('-1 day')));
        $day = strtotime(date('Y-m-d'));

        //查询评论的数量
        $comment = Db::name('user_comment')->count();
        //查询注册用户数量
        $user = Db::name('user')->count();
        //新生成的订单数量
        $order = Db::name('order_info')->whereBetween('create_time',['between','$yesterday,$day'])->count();
        //商品数量
        $goods = Db::name('goods')->count();
        //品牌数量
        $brand = Db::name('goods_brand')->count();






        //注册变量到模板
        $this->assign(['comment'=>$comment]);
        $this->assign(['user'=>$user]);
        $this->assign(['order'=>$order]);
        $this->assign(['goods'=>$goods]);
        $this->assign(['brand'=>$brand]);
        return $this->fetch();
    }
    
    
}