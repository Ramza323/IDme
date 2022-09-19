<?php
    require($_SERVER['DOCUMENT_ROOT'] .'/'. 'wp-load.php');
    $scope = '';

    (isset($_REQUEST['responder']) ? $scope .= 'responder,' : NULL);
    (isset($_REQUEST['military']) ? $scope .= 'military,' : NULL);
    (isset($_REQUEST['government']) ? $scope .= 'government,' : NULL);
    (isset($_REQUEST['nurse']) ? $scope .= 'nurse,' : NULL);
    $scope = substr_replace($scope ,"", -1);

    global $wpdb;
    $sql  = 'UPDATE idme_configuration SET 
    message = "'.(isset($_REQUEST['message']) ? $_REQUEST['message'] : NULL).'", 
    discount = "'.(isset($_REQUEST['discount']) ? $_REQUEST['discount'] : NULL).'", 
    scope = "'.$scope.'", 
    client_id = "'.(isset($_REQUEST['clientid']) ? $_REQUEST['clientid'] : NULL).'", 
    client_secret = "'.(isset($_REQUEST['clientsecret']) ? $_REQUEST['clientsecret'] : NULL).'", 
    landing = "'.(isset($_REQUEST['landing_redirect']) ? $_REQUEST['landing_redirect'] : NULL).'", 
    checkout = "'.(isset($_REQUEST['checkout_redirect']) ? $_REQUEST['checkout_redirect'] : NULL).'", 
    enable = "'.(isset($_REQUEST['enable']) && $_REQUEST['enable'] == 'on' ? TRUE : FALSE).'", 
    updated_at = CURRENT_TIMESTAMP 
    WHERE id = 1';

    if (!function_exists('dbDelta')) {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    }
    dbDelta($sql);
    echo ' Your settings were stored...';
