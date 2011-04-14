<?php
WaxEvent::add("wax.controller", function(){
  $controller = WaxEvent::data();
  if($controller instanceof TestController){
    Config::set_environment('test');
    $controller->application->initialise_database();
  }
});
?>