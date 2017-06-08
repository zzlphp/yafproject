<?php
use Illuminate\Database\Capsule\Manager as DB;

class TagController extends AbstractController
{
    public $user;
    public function init()
    {
        parent::init();
        $this->user = Yaf\Registry::get(SSN_INFO);
    }
    /**
     * 显示整个后台页面框架及菜单
     *
     * @return string
     */
    public function IndexAction()
    {
    }
    public function TaglistAction()
    {
        $page = ($_GET['pageOffset']+$_GET['pageSize'])/$_GET['pageSize'];
        $pagesize = $_GET['pageSize'];
        $taglist = DB::table('tag')->orderBy('id','desc')->Paginate($pagesize,['*'],'page', $page)->withPath('tag/url')->toArray();
        $taglist['rows'] = $taglist['data'];

        echo json_encode($taglist);
    }
}
