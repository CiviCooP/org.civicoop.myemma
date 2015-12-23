<?php
/**
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */
class CRM_Myemma_BAO_MyEmmaFieldMap extends CRM_Myemma_DAO_MyEmmaFieldMap {

    /**
     * Create a new MyEmmaFieldMap based on array-data
     *
     * @param array $params key-value pairs
     * @return CRM_Myemma_DAO_MyEmmaAccount|NULL
     */
    public static function create($params) {
        $entityName = 'MyEmmaFieldMap';
        $hook = empty($params['id']) ? 'create' : 'edit';

        CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
        $instance = new CRM_Myemma_DAO_MyEmmaFieldMap();
        $instance->copyValues($params);
        $instance->save();
        CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

        return $instance;
    }

    public function delete($useWhere = FALSE) {
        $result = parent::delete($useWhere);
        return $result;
    }
}