<?php
/**
 * Builds the form processor config panel
 *
 */
$pods_api          = pods_api();
$all_pods          = $pods_api->load_pods( array( 'names' => true ) );

?>
<div class="caldera-config-group">
	<label><?php echo __('Pod', 'pods-caldera-forms' ); ?></label>
	<div class="caldera-config-field">		
		<select id="{{_id}}_pod" class="block-input required field-config ajax-trigger" data-autoload="true" data-id="{{_id}}" data-name="{{_name}}" data-before="set_config_{{_id}}" data-callback="rebuild_field_binding" data-action="pods_cf_load_fields" data-target="#pods-binding-{{_id}}" data-event="change" name="{{_name}}[pod]" value="{{pod}}" required>
			<option value=""><?php echo __( 'Select a Pod', 'pods-caldera-forms' ); ?></option>
			<?php foreach ( $all_pods as $name => $label ) { ?>
			<option value="<?php echo $name; ?>"{{#is pod value="<?php echo $name;?>"}} selected="selected"{{/is}}><?php echo $label; ?></option>
			<?php } ?>
		</select>
	</div>
</div>

<div class="caldera-config-group">
	<label><?php echo __( 'Item ID', 'pods-caldera-forms' ); ?> </label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config magic-tag-enabled" name="{{_name}}[pod_id]" value="{{pod_id}}">
	</div>
	<span class="block-help"><?php echo __( 'ID of Pod Item to Edit. Leave blank to create new item.', 'pods-caldera-forms' ); ?></span>
</div>

<div id="pods-binding-{{_id}}">
</div>
{{#script}}
	{{#if object_fields}}
	var config_{{_id}} = { {{pod}} : {
	{{#each object_fields}}
		'{{@key}}' : "{{this}}",
	{{/each}}
	{{#each fields}}
		'{{@key}}' : "{{this}}",
	{{/each}}	
		'_all_' : true
	} };
	{{/if}}
	function set_config_{{_id}}(el, ev){
		if(typeof config_{{_id}} !== 'undefined'){
			jQuery(el).data('fields', JSON.stringify( config_{{_id}} ) );
		}
		return true;
	}
{{/script}}
