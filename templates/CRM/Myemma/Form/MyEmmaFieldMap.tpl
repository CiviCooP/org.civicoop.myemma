<h3>Field mapping</h3>

<div class="crm-block crm-form-block crm-my-emma-field-map-form-block">
    {if $action eq 8}
        <div class="">
            <div class="icon inform-icon"></div>
            {ts}Deleting a field map cannot be undone.{/ts} {ts}Do you want to continue?{/ts}
        </div>
        <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="bottom"}</div>
    {else}
        <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="top"}</div>

        <div class="crm-section">
            <div class="label">{$form.my_emma_field.label}</div>
            <div class="content">{$form.my_emma_field.html}</div>
            <div class="clear"></div>
        </div>

        <div class="crm-section">
            <div class="label">{$form.civicrm_field.label}</div>
            <div class="content">{$form.civicrm_field.html}</div>
            <div class="clear"></div>
        </div>

        <div class="crm-section hiddenElement" id="sectionAutocompleteOptionGroup">
            <div class="label">{$form.autocomplete_option_list.label}</div>
            <div class="content">{$form.autocomplete_option_list.html}</div>
            <div class="clear"></div>
        </div>

        <div class="crm-section hiddenElement" id="sectionLocationType">
            <div class="label">{$form.location_type_id.label}</div>
            <div class="content">{$form.location_type_id.html}</div>
            <div class="clear"></div>
        </div>


        <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="bottom"}</div>
    {/if}
</div>

<script type="text/javascript">

    var field_info = {$field_info};

    {literal}
    cj(function() {
        cj('select#civicrm_field').change(triggerFieldChange);
        triggerFieldChange();
    });


    function triggerFieldChange() {
        cj('#sectionLocationType').addClass('hiddenElement');
        cj('#sectionAutocompleteOptionGroup').addClass('hiddenElement');
        var val = cj('#civicrm_field').val();
        var field = field_info[val];
        if (typeof field.hasLocationType != 'undefined' && field.hasLocationType) {
            cj('#sectionLocationType').removeClass('hiddenElement');
        }
        if (typeof field.pseudoconstant != 'undefined' && typeof field.pseudoconstant.optionGroupName != 'undefined') {
            cj('#sectionAutocompleteOptionGroup').removeClass('hiddenElement');
        }
    }
    {/literal}
</script>