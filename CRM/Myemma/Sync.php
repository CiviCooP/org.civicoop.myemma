<?php
/**
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */
class CRM_Myemma_Sync {

    protected $emma;

    protected $group_custom_field;

    protected $group_custom_field_name;

    protected $contact_custom_field;

    protected $parent_group_id;

    protected $field_maps;

    protected $civiFields;

    public $createdContacts;
    public $updatedContacts;
    public $failedContacts;
    public $synchronisedGroups;

    public function __construct($account_id) {
        $account = civicrm_api3('MyEmmaAccount', 'getsingle', array('id' => $account_id));
        if (!$account) {
            throw new Exception('Account not found');
        }
        $this->emma = new CRM_Myemma_Emma($account['account_id'], $account['public_key'], $account['private_key'], false);

        $this->contact_custom_field = CRM_Myemma_BAO_MyEmmaAccount::createCustomField('myemma', $account['name'], $account_id);
        $this->group_custom_field = CRM_Myemma_BAO_MyEmmaAccount::createCustomField('myemma_group', $account['name'], $account_id);

        $this->group_custom_field_name = civicrm_api3('CustomField', 'getvalue', array('return' => 'column_name', 'id' => $this->group_custom_field));

        $this->parent_group_id = $account['parent_group_id'];

        $this->civiFields = CRM_Myemma_Utils::buildCiviCRMFieldList();

        $this->field_maps = array();
        $dao = new CRM_Myemma_DAO_MyEmmaFieldMap();
        $dao->account_id = $account_id;
        $dao->find();
        while($dao->fetch()) {
            $map = array();
            CRM_Core_DAO::storeValues($dao, $map);
            $this->field_maps[$map['my_emma_field']] = $map;
        }
    }

    public function sync() {
        $this->createdContacts = 0;
        $this->updatedContacts = 0;
        $this->failedContacts = 0;
        $this->synchronisedGroups = 0;

        $this->autocompletOptions();

        $this->syncContacts();

        $this->syncGroups();
    }

    public function syncGroups() {
        $custom = 'custom_'.$this->group_custom_field;
        $emmaGroup = clone $this->emma;
        $groups = $emmaGroup->myGroups();
        $groups = json_decode($groups);
        foreach($groups as $group) {
            $groupId = false;
            try {
                $group_params = array();
                $group_params[1] = array($group->member_group_id, 'Integer');
                $groupId = CRM_Core_DAO::singleValueQuery("SELECT entity_id FROM `civicrm_value_myemma_group` WHERE `{$this->group_custom_field_name}` = %1", $group_params);
            } catch (Exception $e) {
                //not found do nothing
                throw $e;
            }

            if ($groupId) {
                civicrm_api3('Group', 'create', array(
                    'id' => $groupId,
                    'title' => $group->group_name,
                    'group_type' => CRM_Core_DAO::VALUE_SEPARATOR.'2'.CRM_Core_DAO::VALUE_SEPARATOR, //mailing list
                ));
            } else {
                $group_params = array();
                $group_params[$custom] = $group->member_group_id;
                $group_params['title'] = $group->group_name;
                $group_params['group_type'] = CRM_Core_DAO::VALUE_SEPARATOR.'2'.CRM_Core_DAO::VALUE_SEPARATOR; //mailing list
                if ($this->parent_group_id) {
                    $group_params['parents'] = $this->parent_group_id;
                }
                $result = civicrm_api3('Group', 'create', $group_params);
                $groupId = $result['id'];
            }

            $this->syncGroupMembers($groupId, $group->member_group_id);

            $this->synchronisedGroups++;
        }
    }

    protected function syncGroupMembers($groupId, $memberGroupId) {
        $batchSize = 500;
        $start = 0;
        $contactIds = array();
        $emmaMemberCountConnection = clone $this->emma;
        $memberCount = $emmaMemberCountConnection->groupsGetMembers($memberGroupId, array('count' => 'true'));
        do {
            $end = $start + $batchSize;
            //we have to clone the emma api object otherwise parameters from previous calls
            //will be used
            $memberSync = clone $this->emma;
            $members = $memberSync->groupsGetMembers($memberGroupId, array(
                'start' => $start,
                'end' => $end,
            ));
            $members = json_decode($members, true);
            foreach($members as $member) {
                $contact_id = $this->getContactIdByMemberId($member['member_id']);
                if ($contact_id) {
                    $contactIds[] = $contact_id;
                }
            }
            $start = $start + $batchSize;
        } while ($start <= $memberCount);

        $currentGroupMembers = civicrm_api3('GroupContact', 'get', array(
            'group_id' => $groupId,
            'status' => "Added",
            'options' => array('limit' => 0),
        ));

        foreach($currentGroupMembers['values'] as $groupMember) {
            $contact_id = $groupMember['contact_id'];
            if(($key = array_search($contact_id, $contactIds)) !== false) {
                unset($contactIds[$key]);
            } else {
                //remove contact from group
                civicrm_api3('GroupContact', 'delete', array(
                    'contact_id' => $contact_id,
                    'group_id' => $groupId,
                ));
            }
        }

        foreach($contactIds as $contact_id) {
            civicrm_api3('GroupContact', 'create', array(
                'contact_id' => $contact_id,
                'group_id' => $groupId,
            ));
        }
    }

    public function syncAllContacts() {
        //we have to clone the emma api object otherwise parameters from previous calls
        //will be used
        $emmaMemberCountConnection = clone $this->emma;
        $memberCount = $emmaMemberCountConnection->myMembers(array('count' => 'true'));
        $batchSize = 500;
        $start = 0;
        do {
            $end = $start + $batchSize;
            $this->syncContacts($start, $end);
            $start = $start + $batchSize;
        } while ($start <= $memberCount);
    }

    public function syncContacts($start, $end) {
        $memberSync = clone $this->emma;
        $members = $memberSync->myMembers(array(
            'start' => $start,
            'end' => $end,
        ));
        $members = json_decode($members, true);

        foreach($members as $member) {
            $this->syncContact($member);
        }
    }

    protected function getContactIdByMemberId($member_id) {
        $custom = 'custom_'.$this->contact_custom_field;
        $find_existing_params = array();
        $find_existing_params['return'] = 'id';
        $find_existing_params[$custom] = $member_id;
        try {
            $contact_id = civicrm_api3('Contact', 'getvalue', $find_existing_params);
        } catch (Exception $e) {
            return false;
        }

        return $contact_id;
    }

    protected function getContactParameters($member) {
        $custom = 'custom_'.$this->contact_custom_field;
        $contact_params = array();
        $email_params = array();
        $phone_params = array();
        $address_params = array();

        $contact_id = $this->getContactIdByMemberId($member['member_id']);
        if ($contact_id) {
            $contact_params['id'] = $contact_id;
        } else {
            $contact_params['contact_type'] = 'Individual';
            $contact_params[$custom] = $member['member_id'];
        }

        if ($member['status'] == 'opt-out') {
            $contact_params['is_opt_out'] = '1';
        }

        $my_emma_fields = $member['fields'];
        $my_emma_fields['email'] = $member['email'];
        foreach($my_emma_fields as $field_name => $value) {
            $civiKey = isset($this->field_maps[$field_name]) && isset($this->field_maps[$field_name]['civicrm_field']) ? $this->field_maps[$field_name]['civicrm_field'] : false;
            if (!$civiKey) {
                continue;
            }

            $civiEntity = false;
            $civiValue = $value;
            $location_type_id = $this->field_maps[$field_name]['location_type_id'] ? $this->field_maps[$field_name]['location_type_id'] : false;
            if ($location_type_id) {
                list($civiEntity, $_) = explode(",", $this->civiFields[$civiKey]['where']);
            }

            if (!empty($this->civiFields[$civiKey]['pseudoconstant']) && !empty($this->civiFields[$civiKey]['pseudoconstant']['optionGroupName'])) {
                if ($this->civiFields[$civiKey]['html_type'] == 'Multi-Select' && !is_array($value)) {
                    $civiValue = explode(",", $value);
                } elseif (is_array($value)) {
                    $civiValue = array_values($value);
                }

                if (is_array($civiValue)) {
                    foreach($civiValue as $k=>$v) {
                        $v = $this->lookupValue($this->civiFields[$civiKey]['pseudoconstant']['optionGroupName'], $v, $this->field_maps[$field_name]['autocomplete_option_list']);
                        if (!$v) {
                            unset($civiValue[$k]);
                        }
                    }
                } else {
                    $civiValue = $this->lookupValue($this->civiFields[$civiKey]['pseudoconstant']['optionGroupName'], $civiValue, $this->field_maps[$field_name]['autocomplete_option_list']);
                }
            }


            switch ($civiEntity) {
                case 'civicrm_email':
                    $email_params[$location_type_id][$civiKey] = $civiValue;
                    break;
                case 'civicrm_phone':
                    $phone_params[$location_type_id][$civiKey] = $civiValue;
                    break;
                case 'civicrm_address':
                    $phone_params[$location_type_id][$civiKey] = $civiValue;
                    break;
                default:
                    $contact_params[$civiKey] = $civiValue;
                    break;
            }
        }

        if (empty($contact_params['first_name']) && empty($contact_params['last_name']) && empty($contact_params['id'])) {
            $contact_params['email'] = $member['email'];
        }

        return array(
            'contact' => $contact_params,
            'address' => $address_params,
            'phone' => $phone_params,
            'email' => $email_params,
        );
    }


    protected function syncContact($member) {
        $params = $this->getContactParameters($member);
        $contact_params = $params['contact'];
        $address_params = $params['address'];
        $phone_params = $params['phone'];
        $email_params = $params['email'];

        $transaction = new CRM_Core_Transaction();

        try {
            $contact = civicrm_api3('Contact', 'create', $contact_params);
            foreach ($email_params as $location_type_id => $params) {
                $params['location_type_id'] = $location_type_id;
                $params['contact_id'] = $contact['id'];
                civicrm_api3('Email', 'create', $params);
            }
            foreach ($phone_params as $location_type_id => $params) {
                $params['location_type_id'] = $location_type_id;
                $params['contact_id'] = $contact['id'];
                civicrm_api3('Phone', 'create', $params);
            }
            foreach ($address_params as $location_type_id => $params) {
                $params['location_type_id'] = $location_type_id;
                $params['contact_id'] = $contact['id'];
                civicrm_api3('Address', 'create', $params);
            }

            if (!empty($contact_params['id'])) {
                $this->updatedContacts ++;
            } else {
                $this->createdContacts++;
            }
            $transaction->commit();
        } catch (Exception $e) {
            //do nothing
            $transaction->rollback();
            $this->failedContacts ++;
        }
    }

    protected function autocompletOptions() {
        $civiFields = CRM_Myemma_Utils::buildCiviCRMFieldList();
        $myEmmaFields = $this->emma->myFields();
        $myEmmaFields = json_decode($myEmmaFields);
        foreach($myEmmaFields as $field) {
            $key = $field->shortcut_name;
            if (!empty($this->field_maps[$key]) && !empty($this->field_maps[$key]['autocomplete_option_list'])) {
                $civi_field = $civiFields[$this->field_maps[$key]['civicrm_field']];
                if (!empty($civi_field['pseudoconstant']) && !empty($civi_field['pseudoconstant']['optionGroupName'])) {
                    foreach($field->options as $key => $value) {
                        $this->lookupValue($civi_field['pseudoconstant']['optionGroupName'], $value, true);
                    }

                }
            }
        }
    }

    protected function lookupValue($option_group_name, $value, $autocreate=false) {
        $option_group_id = civicrm_api3('OptionGroup', 'getvalue', array('return' => 'id', 'name' => $option_group_name));
        try {
            civicrm_api3('OptionValue', 'getsingle', array('option_group_id' => $option_group_id, 'value' => $value));
        } catch (Exception $e) {
            if ($autocreate) {
                civicrm_api3('OptionValue', 'create', array(
                    'option_group_id' => $option_group_id,
                    'value' => $value,
                    'name' => $value,
                    'label' => $value
                ));
                return $value;
            } else {
                return false;
            }
        }
        return $value;
    }

}