jQuery('#setup').submit(function(e) {
    e.preventDefault();
    console.log(jQuery(this).serialize());
    var dialog;
    console.log('click');
    jQuery.ajax({
        type: "POST",
        url: '../wp-content/plugins/idme-mindtrust/inc/database.php',
        data: jQuery(this).serialize() + "&status=save",
        success: function(response)
        {
            dialog = bootbox.dialog({
                message: '<p class="text-center mb-0"><i class="fa fa-spin fa-cog"></i>'+response+'</p>',
                closeButton: false
            });
        }
   });
   setTimeout(function(){
    dialog.modal('hide');
}, 3000);
});