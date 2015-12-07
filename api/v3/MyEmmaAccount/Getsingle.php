<?php

/**
 * MyEmmaAccount.Getsingle API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_my_emma_account_Getsingle_spec(&$spec) {
  $spec['id']['api.required'] = 1;
}

/**
 * MyEmmaAccount.Getsingle API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_my_emma_account_Getsingle($params) {
  if (array_key_exists('id', $params) && $params['id'] == '1') {
    $returnValues = array( // OK, return several data rows
      1 => array(
        'id' => 1,
        'name' => 'My emma account',
        'account_id' => '1',
        'publick_key' => 'MY_PUB_KEY',
        'private_key' => 'MY_PRIVATE_KEY',
      ),
    );
    return civicrm_api3_create_success($returnValues, $params, 'MyEmmaAccount', 'getsingle');
  } else {
    throw new API_Exception('Invalid account id');
  }
}

