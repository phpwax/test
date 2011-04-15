<?php
class WildfireTest extends WaxModel{
  static public $adapter = false;
  static public $db_settings = false;
  static public $db = false;
  static public $test_db = false;
  
  public function setup(){
    $this->define("test_name", "CharField", array("scaffold"=>true));
    $this->define("url", "CharField");
    $this->define("model_class", "CharField", array("widget"=>"SelectInput"));
    $this->define("model_id", "IntegerField");
    $this->define("valid", "IntegerField");
    
    
    $this->define("last_run", "DateTimeField", array("editable"=>false));
    $this->define("last_successful_run", "DateTimeField", array("editable"=>false));
    
    $this->define("last_hash_test_present", "CharField", array("editable"=>false));
    
    $this->define("last_app_hash", "CharField", array("editable"=>false));
    $this->define("last_successful_app_hash", "CharField", array("editable"=>false));
  }
  
  //special hook to push this model to the test database in config.
  function __construct($params=null){
    if(!self::$test_db){
      self::$db = false;
      Config::set_environment('test');
      $config = Config::get('db');
      self::load_adapter($config);
      parent::__construct($params);
      self::$test_db = self::$db;
      Config::set_environment(ENV);
    }else parent::__construct($params);
  }
  
  public static function get_hash($dir){
    //exec("cd $dir; git rev-parse HEAD;", $ret);
    //print_r($ret); exit;
  }
  
  public function run_test(){
    $model = new $this->model_class($this->model_id);
    $test_result = $model->validate();
    $this->last_run = date("Y-m-d H:i:s");
    if($test_result == $this->valid) $this->last_successful_run = date("Y-m-d H:i:s");
    $this->save();
    return $test_result;
  }
  
  public function list_models(){
    
  }
}
?>