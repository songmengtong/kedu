<?php

/**
 *  登陆页
 * @file   Login.php  
 * @date   2016-8-23 19:52:46 
 * @author Zhenxun Du<5552123@qq.com>  
 * @version    SVN:$Id:$ 
 */

namespace app\admin\controller;

use app\admin\model\AdminUser;
use think\Controller;
use think\Db;
use think\Loader;

class Login extends Controller {

    /**
     * 登入
     */
    public function index() {
        //dump(request()->ip());exit;


        if (isset($_POST['dosubmit'])) {
            $username = input('post.username');
            $password = input('post.password');

            if (!$username) {
                $this->error('用户名不能为空');
            }
            if (!$password) {
                $this->error('密码不能为空');
            }

            $info = db('admin_user')->field('id,user_name,password,salt')->where('user_name', $username)->find();


            if (!$info) {
                $this->error('用户不存在');
            }

            if (md5($password.$info['salt']) != $info['password']) {
                $this->error('密码不正确');
            } else {
                session('user_name', $info['user_name']);
                session('user_id', $info['id']);
                session('user_salt', $info['salt']);


                if (input('post.islogin')) {
                    cookie('user_name', md5($info['user_name']));
                    cookie('user_id', md5($info['id']));
                }

                //修改最后登录信息
                $model = new AdminUser();
                $data = [
                    'last_login'=>time(),
                    'last_ip'=>request()->ip()
                ];
                $mod = ['id'=>$info['id']];
                $model->save($data,$mod);
                $this->success('登入成功', 'index/index');
            }
        } else {
            if (session('user_name')) {
                $this->success('您已登入', 'index/index');
            }

            if (cookie('user_name')) {
                $username = md5(cookie('user_name'),'DECODE');
                $info = db('admin')->field('id,user_name,password')->where('user_name', $username)->find();
                if ($info) {
                    //记录
                    session('user_name', $info['username']);
                    session('user_id', $info['id']);
                    Loader::model('Admin')->editInfo(1, $info['id']);
                    $this->success('登入成功', 'index/index');
                }
            }

            $this->view->engine->layout(false);
            return $this->fetch('login');
        }
    }
    /**
     * QQ登入
     */
    public function userinfo() {
        return view();
    }
    /**
     * 登出
     */
    public function logout() {
        session('user_name', null);
        session('user_id', null);
        session('user_salt', null);
        cookie('user_name', null);
        cookie('user_id', null);
        cookie('user_salt', null);
        $this->success('退出成功', 'login/index');
    }
}
