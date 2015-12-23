<?php

/**
 * MyEmmaAccount.Delete API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_my_emma_field_map_delete($params) {
  return _civicrm_api3_basic_delete('CRM_Myemma_BAO_MyEmmaFieldMap', $params);
}

