<?php
declare (strict_types = 1);

namespace app\admin\controller;

use think\Request;
use app\BaseController;
use think\facade\View;
use app\common\model\User as UserModel;
class User extends BaseController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(Request $request)
    {
        $userModel = new UserModel;
        $userModel = $userModel->where('status','<',9);
        //判断搜索关键字，并执行姓名或昵称信息搜索
        $keyword = $request->get('keyword');
        if(!empty($keyword)){
            $userModel = $userModel->where('username','like',"%{$keyword}%")->whereOr('nickname','like',"%{$keyword}%");
        }
        //封装分页信息（5条数据一页，搜索条件）
        $data = $userModel->paginate(['list_rows'=>5,'query'=>$request->param()]);
        //dump($userList);
        // 获取分页信息和数据
        $page = $data->render();
        $userList = $data->items();
        //将信息放置到模板中
        View::assign('volist',$userList);
        View::assign('page',$page);
        return View::fetch();
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return View::fetch();
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $data['username'] = $request->post('username');
        $data['nickname'] = $request->post('nickname');
        $data['password_salt'] = mt_rand(100000,999999);
        $data['password_hash'] = md5($request->post('pass').$data['password_salt']);
        $data['status'] = 1;
        $data['create_at'] = date('Y-m-d H:i:s');
        $data['update_at'] = date('Y-m-d H:i:s');
        //dump($data);
        //执行信息添加
        $user = new UserModel;
        if($user->save($data)){
            return $this->success('添加成功','/admin/user');
        }else{
            return $this->error('添加失败');
        }
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $user = UserModel::where('id',$id)->find();
        //dump($user);
        View::assign('user',$user);
        return View::fetch();
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->put(['username','nickname','status']);
        $data['update_at'] = date('Y-m-d H:i:s');
        $user = UserModel::find($id);
        if($user->save($data)){
            return $this->success('修改成功','/admin/user');
        }else{
            return $this->error('修改失败');
        }
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $user = UserModel::find($id);
        $res = $user->save(['status'=>9,'update'=>date('Y-m-d H:i:s')]);
        if($res){
            return ['error'=>'yes'];
        }else{
            return ['error'=>'no'];
        }
    }
}
