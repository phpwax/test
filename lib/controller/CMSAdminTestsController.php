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
    $db_backup = WaxModel::$db;
    WaxModel::$db = WildfireTest::$test_db;
    WaxEvent::run("cms.form.setup", $this);
    if($this->model->id){
      if($this->model->is_runnable()) $this->model->run_test();
      elseif($this->model->test_name) Session::add_message("Can't run {$this->model->test_name}, please check it's data setup");
    }else foreach($this->model->all() as $row){
      if($row->is_runnable()) $row->run_test();
      elseif($row->test_name) Session::add_message("Can't run {$row->test_name}, please check it's data setup");
    }
    WaxModel::$db = $db_backup;
    $this->redirect_to("/admin/tests/");
  }
  
  public function _data(){
    $this->test_models = array();
    //check all user/app classes
    $classes_to_check = array_diff_key(Autoloader::$registry, array("framework"=>0, "plugin"=>0));
    //only include models
    foreach($classes_to_check as $role => $classes) foreach($classes as $class => $path) if(is_subclass_of($class, "WaxModel")) $this->test_models[] = $class;
    
    //fetch all rows to choose one for this test
    if($model_class = $this->model->model_class){
      $this->data_rows = $model_class::find("all");
      foreach($this->data_rows->model->columns as $col_name => $col_data) if($col_data[1]['scaffold']) $this->data_scaffold[$col_name] = true;
    }
  }
}
?>