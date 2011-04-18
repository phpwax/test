<?php
class WildfireTest extends WaxModel{
  static public $adapter = false;
  static public $db_settings = false;
  static public $db = false;
  static public $test_db = false;
  
  public function setup(){
    $this->define("status", "IntegerField", array("scaffold"=>true, "editable"=>false, "default"=>0, "choices"=>array(0=>"Draft/Revision",1=>"Live")));
    $this->define("test_name", "CharField", array("scaffold"=>true, "default"=>""));
    $this->define("url", "CharField", array("editable"=>false));
    $this->define("model_class", "CharField", array("group"=>"Data", "editable"=>false));
    $this->define("model_id", "IntegerField", array("group"=>"Data", "widget"=>"HiddenInput"));
    $this->define("valid", "IntegerField", array("widget"=>"CheckboxInput"));
    
    
    $this->define("last_run", "DateTimeField", array("editable"=>false, "scaffold"=>true));
    $this->define("last_successful_run", "DateTimeField", array("editable"=>false, "scaffold"=>true));
    
    $this->define("last_app_hash", "CharField", array("editable"=>false));
    $this->define("last_successful_app_hash", "CharField", array("editable"=>false));
  }
  
  //special hook to push this model to the test database in config.
  function __construct($params=null){
    if(!static::$test_db){
      static::$db = false;
      Config::set_environment('test');
      static::load_adapter(Config::get('db'));
      parent::__construct($params);
      static::$test_db = self::$db;
      Config::set_environment(ENV);
    }else parent::__construct($params);
  }
  
  public function before_save(){
    if(!$this->test_name) $this->test_name = ""; //default for "empty" models to work in the CMS
    
    //if there were changes to the test's data, reset the last run times and status
    if($this->primval() && ($old_model = self::find($this->primval()))){
      if($this->model_class != $old_model->model_class || $this->model_id != $old_model->model_id || $this->valid != $old_model->valid){
        $this->status = 0;
        $this->last_run = false;
        $this->last_successful_run = false;
        $this->last_app_hash = false;
        $this->last_successful_app_hash = false;
      }
    }
  }
  
  public static function get_hash($dir){
    //exec("cd $dir; git rev-parse HEAD;", $ret);
    //print_r($ret); exit;
  }
  
  public static function model_list(){
    //check all user/app classes
    $classes_to_check = array_diff_key(Autoloader::$registry, array("framework"=>0, "plugin"=>0));
    //only include models
    foreach($classes_to_check as $role => $classes) foreach($classes as $class => $path) if(is_subclass_of($class, "WaxModel")) $ret[] = $class;
    return $ret;
  }
  
  public function run_test(){
    $model = new $this->model_class($this->model_id);
    $test_result = $model->validate();
    $this->last_run = date("Y-m-d H:i:s");
    if($test_result == $this->valid){
      $this->last_successful_run = date("Y-m-d H:i:s");
      $this->status = 1;
    }else $this->status = 0;
    $this->save();
    return $test_result;
  }
  
  public function humanize($column=false){
    if($this->columns[$column][0] == "DateTimeField" && $this->$column == "0000-00-00 00:00:00") return "Never";
    else return parent::humanize($column);
  }
}
?>