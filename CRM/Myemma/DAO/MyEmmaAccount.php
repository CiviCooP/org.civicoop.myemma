<?php

class CRM_Myemma_DAO_MyEmmaAccount extends CRM_Core_DAO {

  /**
   * static instance to hold the table name
   *
   * @var string
   */
  static $_tableName = 'civicrm_my_emma_account';
  /**
   * static instance to hold the field values
   *
   * @var array
   */
  static $_fields = null;
  /**
   * static instance to hold the keys used in $_fields for each field.
   *
   * @var array
   */
  static $_fieldKeys = null;
  /**
   * static instance to hold the FK relationships
   *
   * @var string
   */
  static $_links = null;
  /**
   * static instance to hold the values that can
   * be imported
   *
   * @var array
   */
  static $_import = null;
  /**
   * static instance to hold the values that can
   * be exported
   *
   * @var array
   */
  static $_export = null;
  /**
   * static value to see if we should log any modifications to
   * this table in the civicrm_log table
   *
   * @var boolean
   */
  static $_log = false;
  /**
   * Project Id
   *
   * @var int unsigned
   */
  public $id;
  /**
   * Name of this account
   *
   * @var string
   */
  public $name;
  /**
   * My Emma Account ID
   *
   * @var int
   */
  public $account_id;
  /**
   * Public key
   *
   * @var string
   */
  public $public_key;
  /**
   * Private key
   *
   * @var string
   */
  public $private_key;

  /**
   * class constructor
   *
   * @return civicrm_volunteer_project
   */
  function __construct()
  {
    $this->__table = self::$_tableName;
    parent::__construct();
  }
  /**
   * Returns foreign keys and entity references
   *
   * @return array
   *   [CRM_Core_Reference_Interface]
   */
  static function getReferenceColumns()
  {
    if (!self::$_links) {
      self::$_links = static ::createReferenceColumns(__CLASS__);
    }
    return self::$_links;
  }
  /**
   * Returns all the column names of this table
   *
   * @return array
   */
  static function &fields()
  {
    if (!(self::$_fields)) {
      self::$_fields = array(
        'id' => array(
          'name' => 'id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('id', array('domain' => 'org.civicoop.myemma')),
          'required' => true,
        ) ,
        'name' => array(
          'name' => 'name',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Name', array('domain' => 'org.civicoop.myemma')) ,
          'description' => 'The name of the my emma account',
          'required' => true,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ),
        'account_id' => array(
          'name' => 'account_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Account ID', array('domain' => 'org.civicoop.myemma')) ,
          'description' => 'The ID of the account in my emma',
          'required' => true,
        ) ,
        'public_key' => array(
          'name' => 'public_key',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Public key', array('domain' => 'org.civicoop.myemma')) ,
          'description' => 'The public key of the my emma account',
          'required' => true,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ),
        'private_key' => array(
          'name' => 'private_key',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Private key', array('domain' => 'org.civicoop.myemma')) ,
          'description' => 'The private key of the my emma account',
          'required' => true,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ),
      );
    }
    return self::$_fields;
  }
  /**
   * Returns an array containing, for each field, the arary key used for that
   * field in self::$_fields.
   *
   * @return array
   */
  static function &fieldKeys()
  {
    if (!(self::$_fieldKeys)) {
      self::$_fieldKeys = array(
        'id' => 'id',
        'name' => 'name',
        'account_id' => 'account_id',
        'public_key' => 'public_key',
        'private_key' => 'private_key',
      );
    }
    return self::$_fieldKeys;
  }
  /**
   * Returns the names of this table
   *
   * @return string
   */
  static function getTableName()
  {
    return self::$_tableName;
  }
  /**
   * Returns if this table needs to be logged
   *
   * @return boolean
   */
  function getLog()
  {
    return self::$_log;
  }
  /**
   * Returns the list of fields that can be imported
   *
   * @param bool $prefix
   *
   * @return array
   */
  static function &import($prefix = false)
  {
    if (!(self::$_import)) {
      self::$_import = array();
      $fields = self::fields();
      foreach($fields as $name => $field) {
        if (CRM_Utils_Array::value('import', $field)) {
          if ($prefix) {
            self::$_import['my_emma_account'] = & $fields[$name];
          } else {
            self::$_import[$name] = & $fields[$name];
          }
        }
      }
    }
    return self::$_import;
  }
  /**
   * Returns the list of fields that can be exported
   *
   * @param bool $prefix
   *
   * @return array
   */
  static function &export($prefix = false)
  {
    if (!(self::$_export)) {
      self::$_export = array();
      $fields = self::fields();
      foreach($fields as $name => $field) {
        if (CRM_Utils_Array::value('export', $field)) {
          if ($prefix) {
            self::$_export['my_emma_account'] = & $fields[$name];
          } else {
            self::$_export[$name] = & $fields[$name];
          }
        }
      }
    }
    return self::$_export;
  }

}