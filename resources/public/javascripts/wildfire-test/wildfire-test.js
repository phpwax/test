jQuery(function(){
  jQuery("#data a.operation_Choose, #data tr").click(function(e){
    e.preventDefault();
    jQuery("#data tr.active").removeClass("active");
    jQuery("#data #wildfire_test_model_id").val(jQuery(this).closest("tr").addClass("active").attr("data-model-id"));
  });
});