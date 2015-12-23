<?php
/**
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */
class CRM_Myemma_Utils {

    public static function buildMyEmmaFieldList($account_id) {
        $account = civicrm_api3('MyEmmaAccount', 'getsingle', array('id' => $account_id));
        if (!$account) {
            throw new Exception('Account not found');
        }
        $emma = new CRM_Myemma_Emma($account['account_id'], $account['public_key'], $account['private_key'], false);
        $return = array(
            'email' => ts('E-mail'),
        );
        $fields = json_decode($emma->myFields());
        foreach($fields as $field) {
            $return[$field->shortcut_name] = $field->display_name;
        }

        return $return;
    }

    public static function locationTypes() {
        $location_types = civicrm_api3('LocationType', 'get', array('is_active'));
        $return = array();
        foreach($location_types['values'] as $loc_type) {
            $return[$loc_type['id']] = $loc_type['display_name'];
        }
        return $return;
    }

    public static function buildCiviCRMFieldList() {
        $fields = CRM_Contact_BAO_Contact::importableFields('Individual', FALSE, TRUE);
        foreach($fields as $field_name => $field) {
            if (!empty($field['custom_field_id'])) {
                $custom_field = civicrm_api3('CustomField', 'getsingle', array('id' => $field['custom_field_id']));
                if (!empty($custom_field['option_group_id'])) {
                    $fields[$field_name]['pseudoconstant'] = array(
                        'optionGroupName' => civicrm_api3('OptionGroup', 'getvalue', array('return' => 'name', 'id' => $custom_field['option_group_id'])),
                    );
                }
            }
        }
        return $fields;
    }
}