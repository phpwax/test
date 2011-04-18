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
    $this->define("model_class", "CharField", array("group"=>"Data"));
    $this->define("model_id", "IntegerField", array("group"=>"Data"));
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
  
  public static function get_hash($dir){
    //exec("cd $dir; git rev-parse HEAD;", $ret);
    //print_r($ret); exit;
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
  
  public function list_models(){
    
  }
  
  public function humanize($column=false){
    if($this->columns[$column][0] == "DateTimeField" && $this->$column == "0000-00-00 00:00:00") return "Never";
    else return parent::humanize($column);
  }
}
?>