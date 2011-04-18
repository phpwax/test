<?php
class CMSAdminTestsController extends AdminComponent{
  public $module_name = "tests";
  public $model_class = 'WildfireTest';
  public $display_name = "Tests";
  public $dashboard = false;
  public $operation_actions = array('edit', 'run_test');
  
  public function events(){
    parent::events();
    WaxEvent::add("cms.layout.sublinks", function(){
      $obj = WaxEvent::data();
      $obj->quick_links["Run All Tests"] = "/admin/tests/run_test/";
    });
  }
  
  public function run_test(){
    WaxEvent::run("cms.form.setup", $this);
    if($this->model->id) $this->model->run_test();
    else foreach($this->model->all() as $row) $row->run_test();
    $this->redirect_to("/admin/tests/");
  }
  
}
?>