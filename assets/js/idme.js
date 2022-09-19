function GetURLParameter(sParam)
{
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split('&');
    for (var i = 0; i < sURLVariables.length; i++) 
    {
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == sParam) 
        {
            return sParameterName[1];
        }
    }
}

function setCookie(cname, cvalue, exdays) {
    const d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    let expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";"  + ";path=/";
  }

function getCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for(let i = 0; i <ca.length; i++) {
      let c = ca[i];
      while (c.charAt(0) == ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return "";
  }

jQuery( document ).ready(function() {
    if (getCookie("verification") != ''){
        jQuery("#idme-verification").slideUp();
        jQuery(".idme-verification_group").slideUp("slow");
        jQuery(".fee").slideDown("slow");
        var endpoint = window.location.origin + '/wp-json/idme/v1/verified'
        jQuery.ajax({
            method: 'POST',
            url: endpoint,
            data: {
                code: code,
                uri: uri
            }
        }).done(function(msg) {

        })
        .fail(function(msg) {
            console.log(msg)
        })
    } else {
        var endpoint = window.location.origin + '/wp-json/idme/v1/verification'
        var code = GetURLParameter('code');
        var pathArray = window.location.pathname.split('/');
        var uri = pathArray[1];
        jQuery.ajax({
            method: 'POST',
            url: endpoint,
            data: {
                code: code,
                uri: uri
            }
        }).done(function(msg) {
            console.log(msg);
            if(msg == 'verified'){
                jQuery("#idme-verification").slideUp();
                jQuery(".idme-verification_group").slideUp("slow");
                jQuery(".fee").slideDown();
                setCookie("verification", msg);
                jQuery('body').trigger('update_checkout');  
            }
        })
        .fail(function(msg) {
            //console.log(msg)
        })
    }
});