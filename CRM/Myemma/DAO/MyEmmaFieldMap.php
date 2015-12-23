<?php

class CRM_Myemma_DAO_MyEmmaFieldMap extends CRM_Core_DAO {

    /**
     * static instance to hold the table name
     *
     * @var string
     */
    static $_tableName = 'civicrm_my_emma_field_map';
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
     * My Emma Account ID
     *
     * @var int
     */
    public $account_id;
    /**
     * Civicrm Field
     *
     * @var string
     */
    public $civicrm_field;
    /**
     * Civicrm Field Option
     *
     * @var array
     */
    public $autocomplete_option_list;
    /**
     * Civicrm location type
     *
     * @var int
     */
    public $location_type;
    /**
     * My Emma Field ID
     *
     * @var string
     */
    public $my_emma_field;


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
                'account_id' => array(
                    'name' => 'account_id',
                    'type' => CRM_Utils_Type::T_INT,
                    'title' => ts('My Emma Account', array('domain' => 'org.civicoop.myemma')) ,
                    'description' => '',
                    'required' => true,
                ) ,
                'civicrm_field' => array(
                    'name' => 'civicrm_field',
                    'type' => CRM_Utils_Type::T_STRING,
                    'title' => ts('CiviCRM Field', array('domain' => 'org.civicoop.myemma')) ,
                    'description' => '',
                    'required' => true,
                    'maxlength' => 255,
                    'size' => CRM_Utils_Type::HUGE,
                ),
                'autocomplete_option_list' => array(
                    'name' => 'autocomplete_option_list',
                    'type' => CRM_Utils_Type::T_BOOLEAN,
                    'title' => ts('Autocomplete option value', array('domain' => 'org.civicoop.myemma')) ,
                    'description' => '',
                    'required' => true,
                    'maxlength' => 255,
                    'size' => CRM_Utils_Type::HUGE,
                ),
                'location_type_id' => array(
                    'name' => 'location_type_id',
                    'type' => CRM_Utils_Type::T_INT,
                    'title' => ts('Location Type') ,
                    'description' => 'Which Location does this field belong to.',
                    'html' => array(
                        'type' => 'Select',
                    ) ,
                    'pseudoconstant' => array(
                        'table' => 'civicrm_location_type',
                        'keyColumn' => 'id',
                        'labelColumn' => 'display_name',
                    )
                ) ,
                'my_emma_field' => array(
                    'name' => 'my_emma_field',
                    'type' => CRM_Utils_Type::T_STRING,
                    'title' => ts('Field ID', array('domain' => 'org.civicoop.myemma')) ,
                    'description' => '',
                    'required' => true,
                    'maxlength' => 255,
                    'size' => CRM_Utils_Type::HUGE,
                ) ,
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
                'account_id' => 'account_id',
                'civicrm_field' => 'civicrm_field',
                'autocomplete_option_list' => 'autocomplete_option_list',
                'location_type_id' => 'location_type_id',
                'my_emma_field' => 'my_emma_field',
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
                        self::$_import['my_emma_field_map'] = & $fields[$name];
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
                        self::$_export['my_emma_field_map'] = & $fields[$name];
                    } else {
                        self::$_export[$name] = & $fields[$name];
                    }
                }
            }
        }
        return self::$_export;
    }

}