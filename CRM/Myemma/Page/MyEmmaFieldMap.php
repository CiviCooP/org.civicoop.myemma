<?php
/**
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */
class CRM_Myemma_Page_MyEmmaFieldMap extends CRM_Core_Page_Basic {

    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     * @static
     */
    static $_links = null;

    protected $account_id;

    /**
     * Class constructor.
     *
     * @param string $title
     *   Title of the page.
     * @param int $mode
     *   Mode of the page.
     *
     */
    public function __construct($title = NULL, $mode = NULL) {
        parent::__construct($title, $mode);
        $this->account_id = CRM_Utils_Request::retrieve('account_id', 'Integer', CRM_Core_DAO::$_nullObject, true);
        $this->assign('account_id', $this->account_id);
        $this->assign('civicrm_fields', CRM_Myemma_Utils::buildCiviCRMFieldList());
        $this->assign('location_types', CRM_Myemma_Utils::locationTypes());
        $this->assign('my_emma_fields', CRM_Myemma_Utils::buildMyEmmaFieldList($this->account_id));
    }


    function getBAOName() {
        return 'CRM_Myemma_BAO_MyEmmaFieldMap';
    }

    /**
     * Get action Links
     *
     * @return array (reference) of action links
     */
    function &links() {
        if (!(self::$_links)) {
            self::$_links = array(
                CRM_Core_Action::UPDATE  => array(
                    'name'  => ts('Edit'),
                    'url'   => 'civicrm/admin/my_emma_account/field_map',
                    'qs'    => 'action=update&id=%%id%%&reset=1&account_id='.$this->account_id,
                    'title' => ts('Edit Financial Type'),
                ),
                CRM_Core_Action::DELETE  => array(
                    'name'  => ts('Delete'),
                    'url'   => 'civicrm/admin/my_emma_account/field_map',
                    'qs'    => 'action=delete&id=%%id%%&account_id='.$this->account_id,
                    'title' => ts('Delete Financial Type'),
                ),
            );
        }
        return self::$_links;
    }

    /**
     * Get name of edit form
     *
     * @return string Classname of edit form.
     */
    function editForm() {
        return 'CRM_Myemma_Form_MyEmmaFieldMap';
    }

    /**
     * Get edit form name
     *
     * @return string name of this page.
     */
    function editName() {
        return 'CRM_Myemma_Form_MyEmmaFieldMap';
    }

    /**
     * Get user context.
     *
     * @return string user context.
     */
    function userContext($mode = null) {
        return 'civicrm/admin/my_emma_account/field_map';
    }

    /**
     * Get userContext params.
     *
     * @param int $mode
     *   Mode that we are in.
     *
     * @return string
     */
    public function userContextParams($mode = NULL) {
        return 'reset=1&action=browse&account_id='.$this->account_id;
    }

}