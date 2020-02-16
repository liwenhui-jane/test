<?php
declare (strict_types = 1);

namespace app\admin\controller;

use think\Request;
use app\BaseController;
use think\facade\View;
use app\common\model\Member as MemberModel;
class Member extends BaseController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(Request $request)
    {
        $memberModel = new MemberModel;
        $memberModel = $memberModel->where('status', '<', 9);
        $keyword = $request->get('keyword');
        if ($keyword) {
            $memberModel = $memberModel->where('nickname', 'like', "%{$keyword}%");
        }
        $data = $memberModel->paginate(['list_rows' => 5, 'query' => $request->param()]);
        $memberList = $data->items();
        $page = $data->render();
        View::assign('volist', $memberList);
        View::assign('page', $page);
        return View::fetch();
    }
    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
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
        //
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
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
