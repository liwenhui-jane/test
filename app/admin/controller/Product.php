<?php
declare (strict_types = 1);

namespace app\admin\controller;

use app\BaseController;
use think\Request;
use think\facade\View;
use app\common\model\Product as ProductModel;
use app\common\model\Shop;
use app\common\model\Category;
class Product extends BaseController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(Request $request)
    {
        $productModel = new ProductModel;
        $productModel = $productModel->where('status','<',9);
        $keyword = $request->get('keyword');
        if($keyword){
            $productModel = $productModel->where('name','like',"%{$keyword}%");
        }
        $data = $productModel->paginate(['list_rows'=>5,'query'=>$request->param()]);
        $productList = $data->items();
        $page = $data->render();
        foreach($productList as $product){
            $product->shop = Shop::where('id',$product->shop_id)->value('name');
            $category = Category::find($product->category_id);
            $product->category = $category->name;
        }
        //dump($productList);
        View::assign('volist',$productList);
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
        $shopList = Shop::where('status',"<",9)->select();
        View::assign('shopList',$shopList);
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
        try{
            $file = request()->file('cover_pic');
            validate(['file'=>[
                'fileSize'=>1024000,
                'fileExt'=>'gif,jpg,png'
            ]])->check(['file'=>$file]);
            $cover_pic = \think\facade\Filesystem::disk('product')->putFile('',$file,function(){
                return md5((string)microtime(true));
            });
        }catch(\Exception $e){
            return $this->error($e->getMessage());
        }
        $data = $request->post(['shop_id','category_id','name','price']);
        $data['status'] = 1;
        $data['cover_pic'] = $cover_pic;
        $data['create_at'] = date('Y-m-d H:i:s');
        $data['update_at'] = date('Y-m-d H:i:s');
        $product = new ProductModel;
        if($product->save($data)){
            $this->success('添加成功','/admin/product');
        }else{
            $this->error('添加失败');
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
        $shopList = Shop::where('status',"<",9)->select();
        $product = ProductModel::find($id);
        View::assign('shopList',$shopList);
        View::assign('product',$product);
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
        $data = $request->put(['name','shop_id','category_id','price','status']);
        $data['update_at'] = date('Y-m-d H:i:s');
        //dump($data);
        $product = ProductModel::find($id);
        if($product->save($data)){
            $this->success('修改成功','/admin/product');
        }else{
            $this->error('修改失败');
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
        $product = ProductModel::find($id);
        if($product->save(['status'=>9,'update_at'=>date('Y-m-d H:i:s')])){
            return ['error'=>'yes'];
        }else{
            return ['error'=>'no'];
        }
    }
}
