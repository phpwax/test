<?php
class BaseAdminTestController extends AdminComponent{
  public $module_name = "tests";
  public $model_class = 'WildfireTest';
  public $display_name = "Tests";
  public $dashboard = false;
  
  // public function index(){
  //   foreach((array)param("selected_test") as $id) $this->test_results[$id] = Test::find($id)->run_test();
  //   if($id = param("run_test")) $this->test_results[$id] = Test::find($id)->run_test();
  //   $this->tests = Test::find("all");
  // }
  
  // public function edit_data(){
  //   if($class = param("model_class")){
  //     if(!$page = param("page")) $page = 1;
  //     $this->model = new $class;
  //     $this->recordset = $this->model->page($page, $this->per_page);
  //   }else{
  //     $this->classes = array();
  //     //include all classes (besides the framework, to drop dependance on simpletest)
  //     foreach(array_diff_key(Autoloader::$registry, array("framework"=>0)) as $role => $classes) foreach($classes as $class => $path) Autoloader::include_from_registry($class);
  //     //find all models to list tests on them
  //     foreach(get_declared_classes() as $class){
  //       if(is_subclass_of($class, "WaxModel")){
  //         $this->classes[] = $class;
  //       }
  //     }
  //   }
  // }
}
?>