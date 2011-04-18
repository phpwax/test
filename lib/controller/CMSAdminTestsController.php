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
    WaxEvent::add("cms.save.success", function(){
      if(param('data_source') == "new"){
        $controller = WaxEvent::data();
        $new_model = new $controller->model->model_class;
        $new_model->set_attributes(param($new_model->table));
        
        //DIRTY HACKS START HERE
        $db_backup = WaxModel::$db;
        WaxModel::$db = WildfireTest::$test_db;
        $new_model->insert(); //doesn't call validation
        WaxModel::$db = $db_backup;
        //DIRTY HACKS END HERE (hopefully :)
        
        if($new_model->id){
          $controller->model->model_id = $new_model->id;
          $controller->model->save();
        }
      }
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
    $this->test_models = WildfireTest::model_list();
    
    //fetch all rows from test db to choose one for this test
    $this->data_rows = array();
    $db_backup = WaxModel::$db;
    WaxModel::$db = WildfireTest::$test_db;
    foreach($this->test_models as $model_class) {
      $data_model = new $model_class;
      $this->data_rows[$model_class] = $data_model->all();
      foreach($this->data_rows[$model_class]->model->columns as $col_name => $col_data) if($col_data[1]['scaffold']) $this->data_scaffold[$model_class][$col_name] = true;
    }
    WaxModel::$db = $db_backup;
  }
}
?>