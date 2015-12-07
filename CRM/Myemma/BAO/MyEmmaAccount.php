<?php

class CRM_Myemma_BAO_MyEmmaAccount extends CRM_Myemma_DAO_MyEmmaAccount {

  /**
   * Create a new MyEmmaAccount based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_Myemma_DAO_MyEmmaAccount|NULL
   */
  public static function create($params) {
    $entityName = 'MyEmmaAccount';
    $hook = empty($params['id']) ? 'create' : 'edit';

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
    $instance = new CRM_Myemma_DAO_MyEmmaAccount();
    $instance->copyValues($params);
    $instance->save();
    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    return $instance;
  }
}