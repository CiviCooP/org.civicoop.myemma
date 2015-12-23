<?php
/**
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */
class CRM_Myemma_Form_MyEmmaFieldMap extends CRM_Core_Form {

    protected $_id;

    protected $account_id;

    function preProcess() {
        $this->_id = CRM_Utils_Request::retrieve('id' , 'Integer', $this, FALSE);
        $this->account_id = CRM_Utils_Request::retrieve('account_id' , 'Integer', $this, TRUE);
    }

    public function buildQuickForm() {
        $this->add('hidden', 'account_id', $this->account_id);
        if ($this->_action == CRM_Core_Action::UPDATE) {
            $this->add('hidden', 'id', $this->_id);
        }
        if ($this->_action & CRM_Core_Action::DELETE) {
            $this->addButtons(array(
                    array(
                        'type' => 'next',
                        'name' => ts('Delete'),
                        'isDefault' => TRUE,
                    ),
                    array(
                        'type' => 'cancel',
                        'name' => ts('Cancel'),
                    ),
                )
            );
            return;
        }

        $this->add('select', 'location_type_id', ts('Location type'), $this->locationTypes(), true);
        $this->add('select', 'civicrm_field', ts('Select CiviCRM Field'), $this->buildFieldList(), true);
        $this->add('select', 'autocomplete_option_list', ts('Autocomplete option values?'), array(
            '0' => ts('No'),
            '1' => ts('Yes'),
        ));
        $this->add('select', 'my_emma_field', ts('Select My Emma Field'), $this->buildMyEmmaFieldList(), true);

        $this->addButtons(array(
                array(
                    'type' => 'next',
                    'name' => ts('Save'),
                    'isDefault' => TRUE,
                ),
                array(
                    'type' => 'cancel',
                    'name' => ts('Cancel'),
                ),
            )
        );
    }

    public function buildMyEmmaFieldList() {
        return CRM_Myemma_Utils::buildMyEmmaFieldList($this->account_id);
    }

    public function locationTypes() {
        return CRM_Myemma_Utils::locationTypes();
    }

    public function buildFieldList() {
        $fields = array();
        $contactFields = CRM_Myemma_Utils::buildCiviCRMFieldList();
        $this->assign('field_info', json_encode($contactFields));
        foreach($contactFields as $key => $field) {
            $fields[$key] = $field['title'];
        }
        return $fields;
    }

    public function setDefaultValues() {
        $defaults = array();
        $params = array();

        if (isset($this->_id)) {
            $params = array('id' => $this->_id);
            $bao = new CRM_Myemma_BAO_MyEmmaFieldMap();
            $bao->id = $this->_id;
            if ($bao->find(TRUE)) {
                CRM_Myemma_BAO_MyEmmaFieldMap::storeValues($bao, $defaults);
            }
        }
        return $defaults;
    }

    public function postProcess() {
        if ($this->_action & CRM_Core_Action::DELETE) {
            civicrm_api3('MyEmmaFieldMap', 'delete', array('id' => $this->_id));
            CRM_Core_Session::setStatus(ts('Field removed from my emma sync settings.'), '', 'success');
        } else {
            $params = array( );
            // store the submitted values in an array
            $params = $this->exportValues();
            if ($this->_action & CRM_Core_Action::UPDATE) {
                $params['id'] = $this->_id;
            }
            CRM_Myemma_BAO_MyEmmaFieldMap::create($params);
            CRM_Core_Session::setStatus(ts('Saved field mapping for my emma sync settings'),'', 'success');
        }
    }

}