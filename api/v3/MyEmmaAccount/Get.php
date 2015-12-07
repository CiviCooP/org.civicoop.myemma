<?php

/**
 * MyEmmaAccount.Get API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_my_emma_account_Get($params) {
  $returnValues = array( // OK, return several data rows
    1 => array(
      'id' => 1,
      'name' => 'My emma account',
      'account_id' => '1',
      'publick_key' => 'MY_PUB_KEY',
      'private_key' => 'MY_PRIVATE_KEY',
    ),
  );
  return civicrm_api3_create_success($returnValues, $params, 'MyEmmaAccount', 'get');
}

