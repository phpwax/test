<?php
WaxEvent::add("wax.controller", function(){
  $controller = WaxEvent::data();
  if($controller instanceof BaseTestController){
    Config::set_environment('test');
    $controller->application->initialise_database();
  }
});
?>