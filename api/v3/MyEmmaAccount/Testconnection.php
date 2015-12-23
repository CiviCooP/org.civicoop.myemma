<?php

function civicrm_api3_my_emma_account_Testconnection($params) {
    if (!isset($params['id'])) {
        return civicrm_api3_create_error(ts('No ID given'));
    }

    $account = civicrm_api3('MyEmmaAccount', 'getsingle', array('id' => $params['id']));
    if (!$account) {
        return civicrm_api3_create_error(ts('Account not found'));
    }

    try {
        $emma = new CRM_Myemma_Emma($account['account_id'], $account['public_key'], $account['private_key'], false);
        $emma->myGroups();
        return civicrm_api3_create_success(array(
            'msg' => ts('Successfully connected to my Emma'),
        ));
    } catch (Exception $e) {
        throw $e;
        return civicrm_api3_create_error(ts('Could not connect to MyEmma'));
    }

}