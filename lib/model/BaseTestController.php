<?php
class BaseTestController extends WaxController{
  public $tests = array();
  public $per_page = 10;
  
  public function controller_global(){
    $this->use_layout = "test";
  }
  
  public function index(){
    
    foreach((array)param("selected_test") as $id) $this->test_results[$id] = Test::find($id)->run_test();
    if($id = param("run_test")) $this->test_results[$id] = Test::find($id)->run_test();
    $this->tests = Test::find("all");
  }
  
}
?>