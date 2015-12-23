<?php
/**
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */
function civicrm_api3_my_emma_account_Syncgroups($params) {
    if (!isset($params['id'])) {
        return civicrm_api3_create_error(ts('No ID given'));
    }

    $sync = new CRM_Myemma_Sync($params['id']);
    $sync->syncGroup($params['member_group_id']);

    $value['synchronisedGroups'] = $sync->synchronisedGroups;

    $values[] = $value;

    return civicrm_api3_create_success($values, $params, 'MyEmmaAccount', 'Synccontacts');

}