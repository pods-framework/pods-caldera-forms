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
		<select id="{{_id}}_pod" class="block-input required field-config" name="{{_name}}[pod]" value="{{pod}}" required>
			<option value=""><?php echo __( 'Select a Pod', 'pods-caldera-forms' ); ?></option>
			<?php foreach ( $all_pods as $name => $label ) { ?>
			<option value="<?php echo $name; ?>"{{#is pod value="<?php echo $name;?>"}} selected="selected"{{/is}}><?php echo $label; ?></option>
			<?php } ?>
		</select>
	</div>
</div>
<p>Still working on the field binding. Be here soon.</p>