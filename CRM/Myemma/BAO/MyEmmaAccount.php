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

    $id = $instance->id;

    self::createCustomField('myemma', $instance->name, $id);
    self::createCustomField('myemma_group', $instance->name, $id);

    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    return $instance;
  }

  public function delete($useWhere = FALSE) {
    $id = $this->id;
    $result = parent::delete($useWhere);

    try {
      $custom_params['custom_group_id'] = civicrm_api3('CustomGroup', 'getvalue', array('name' => 'myemma', 'return' => 'id'));
      $custom_params['name'] = 'my_emmma_'.$id;
      $custom_field = civicrm_api3('CustomField', 'getsingle', $custom_params);
      civicrm_api3('CustomField', 'delete', array('id' => $custom_field['id']));
    } catch (Exception $e) {
      //do nothing
    }

    return $result;
  }

  public static function createCustomField($custom_group_name, $name, $id) {
    //create custom field for linking an individual to this account
    $do_update = false;
    try {
      $custom_field = civicrm_api3('CustomField', 'getsingle', array(
          'custom_group_id' => $custom_group_name,
          'name' => "my_emma_".$id,
      ));
      $do_update = true;
    } catch (Exception $e) {
      //do nothing
    }

    if ($do_update) {
      $custom_field_update['id'] = $custom_field['id'];
      $custom_field_update['label'] = 'My Emma ID ('.$name.'): ';
      civicrm_api3('CustomField', 'create', $custom_field_update);

      return $custom_field['id'];
    } else {
      $custom_params['custom_group_id'] = civicrm_api3('CustomGroup', 'getvalue', array('name' => $custom_group_name, 'return' => 'id'));
      $custom_params['name'] = 'my_emma_'.$id;
      $custom_params['html_type'] = 'Text';
      $custom_params['data_type'] = 'Integer';
      $custom_params['is_active'] = '1';
      $custom_params['is_searchable'] = '1';
      $custom_params['label'] = 'My Emma ID ('.$name.'): ';
      $result = civicrm_api3('CustomField', 'create', $custom_params);

      return $result['id'];
    }
  }
}