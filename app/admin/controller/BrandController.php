<?php
namespace app\admin\controller;

use app\admin\model\AreaSelect;
use app\admin\model\PharmacyBrandInfoModel;
use base\controller\AdminBaseController;
use think\Config;
use think\Db;

/**
 * 品牌管理
 * @author 青空
 */
class BrandController extends AdminBaseController
{

    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new PharmacyBrandInfoModel();

    }

    /**
     * 品牌列表
     */
    public function index()
    {

        $key_words = trim($this->request->param('key_words', '', 'string'));
        /**搜索条件**/
        $key_map                                                    = ["isvalid" => 1];
        $key_words != '' && $key_map['pharmacy_name|pharmacy_code'] = ['like', '%' . $key_words . '%'];

        $list = $this->model->where($key_map)->paginate(self::PAGE_SIZE);
        // 获取分页显示
        $this->assign('list', $list);
        $this->assign('page', $list->render());
        return $this->fetch();
    }

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {

            $post   = $this->request->param();
            unset($post['/admin/brand/add_html']);
            $result = $this->model->validate('PharmacyBrandInfo.add')->save($post);
            false == $result && $this->error($this->model->getError());
            //写日志
            service('log', 'insert', ['2', '添加品牌', session('admin_id'), serialize($this->request->param())]);
            $this->success('添加成功', url("brand/index"));
        }

        if ($this->request->isAjax()) {
            if ($this->request->request('type') == 'province') {

                $this->success('', null, AreaSelect::getProvince());
            }
            if ($this->request->request('type') == 'city') {
                $this->success('', null, AreaSelect::getCity($this->request->request('province')));
            }
            if ($this->request->request('type') == 'district') {
                $this->success('', null, AreaSelect::getDistrict($this->request->request('city')));
            }
        }
        $this->assign('qq_map_key', Config::get('qq_map_key'));
        return $this->fetch();
    }

    /**
     * 编辑
     */
    public function edit()
    {

        $id = $this->request->param('id', 0, 'intval');
        if ($this->request->isPost()) {
            $post   = $this->request->param();
            $result = $this->model->allowField(true)->validate('PharmacyBrandInfo.edit')->save($post, ['id' => $post['id']]);
            false === $result && $this->error($this->model->getError());
            //写日志
            service('log', 'insert', ['2', '编辑品牌', session('admin_id'), serialize($this->request->param())]);
            $this->success('编辑成功', url("brand/index"));
        }
        $row = $this->model->get(['id' => $id]);
        !$row && $this->error('数据不存在');
        $city           = Db::table('yb_city_city')->where('id', $row->city)->value('name');
        $row->city_name = $city;
        $this->assign('row', $row);
        $this->assign('qq_map_key', Config::get('qq_map_key'));
        return $this->fetch();
    }
    /**
     * 删除
     */
    public function del()
    {
        $id = $this->request->param('id', 0, 'intval');
        $this->model->where("id", $id)->update(['isvalid' => 0]);
        //写日志
        service('log', 'insert', ['2', '删除品牌', session('admin_id'), serialize($this->request->param())]);
        $this->success("删除成功！");
        return $this->fetch();
    }

}
