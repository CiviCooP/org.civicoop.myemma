{if $action eq 1 or $action eq 2 or $action eq 8}
    {include file="CRM/Myemma/Form/MyEmmaFieldMap.tpl"}
{else}
    <div id="ltype">
        <div class="form-item">
            {strip}
                <table cellpadding="0" cellspacing="0" border="0">
                    <thead class="sticky">
                    <th>{ts}CiviCRM Field{/ts}</th>
                    <th>{ts}My Emma Field{/ts}</th>
                    <th>{ts}Location type{/ts}</th>
                    <th>{ts}Autocomplete option list{/ts}</th>
                    <th></th>
                    </thead>
                    {foreach from=$rows item=row}
                        <tr id="row_{$row.id}"class="{cycle values="odd-row,even-row"} {$row.class}">
                            {assign var=civi_field_name value=$row.civicrm_field}
                            {assign var=my_emma_field_name value=$row.my_emma_field}
                            <td>{$civicrm_fields.$civi_field_name.title}</td>
                            <td>{$my_emma_fields.$my_emma_field_name}</td>
                            <td>
                                {if ($civicrm_fields.$civi_field_name.hasLocationType)}
                                    {assign var=loc_type value=`$row.location_type_id`}
                                    {$location_types.$loc_type}
                                {/if}
                            </td>
                            <td>
                                {if ($civicrm_fields.$civi_field_name.pseudoconstant && $civicrm_fields.$civi_field_name.pseudoconstant.optionGroupName)}
                                    {if $row.autocomplete_option_list}
                                        {ts}Yes{/ts}
                                    {else}
                                        {ts}No{/ts}
                                    {/if}
                                {/if}
                            </td>
                            <td>{$row.action}</td>
                        </tr>
                    {/foreach}
                </table>
            {/strip}
        </div>

        {if $action ne 1 and $action ne 2}
            <div class="action-link">
                <a href="{crmURL q="action=add&reset=1&account_id=`$account_id`"}" id="newFieldMapping" class="button"><span><div class="icon add-icon"></div>{ts}Add field mapping{/ts}</span></a>
            </div>
        {/if}

    </div>
{/if}