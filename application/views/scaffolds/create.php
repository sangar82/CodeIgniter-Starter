<div id='content-top'>
    <h2>Scaffolds</h2>
    <span class='clearFix'>&nbsp;</span>
</div>

<div style='width:600px;float:left;margin-left:30px;'>

	<?php 
	$attributes = array('class' => 'tform', 'id' => '');
	echo form_open_multipart('scaffolds/scaffolds/create', $attributes); 
	?>


	<p>
		<label class='labelform' for="description">Controller Name (Plural) <span class="required">*</span></label>

		<input id="controller_name" type="text" name="controller_name" maxlength="256" value="<?php echo set_value('controller_name'); ?>"  />
		
		<br><?php echo form_error('controller_name'); ?>

	</p>

	<p>
		<label class='labelform' for="description">Model Name (Singular) <span class="required">*</span></label>

		<input id="model_name" type="text" name="model_name" maxlength="256" value="<?php echo set_value('model_name'); ?>"  />
		
		<br><?php echo form_error('model_name'); ?>

	</p>


	<p>
		<label class='labelform' for="description">Scaffold code <span class="required">*</span></label>

		<textarea id="scaffold_code"  name="scaffold_code"  rows='80' class='scaffold_textarea' /><?php echo set_value('scaffold_code', ''); ?></textarea>

		<br><?php echo form_error('scaffold_code'); ?>

	</p>

	<p>
		Opciones:<br><br>
		<input type='checkbox' name='scaffold_delete_bd' id='scaffold_delete_bd' value="<?php echo set_value('scaffold_delete_bd', '1'); ?>" /> <label class='labelforminline' for="scaffold_delete_bd">Borrar tabla en la BD si existe</label><br/>
		<input type='checkbox' name='scaffold_bd' id='scaffold_bd' value='<?php echo set_value('scaffold_bd' , '1'); ?>' /> <label class='labelforminline' for="scaffold_bd">Crear tabla en la BD</label><br/>
		<input type='checkbox' name='scaffold_routes' id='scaffold_routes' value='<?php echo set_value('scaffold_routes' , '1'); ?>' /> <label class='labelforminline' for="scaffold_routes">Modificar archivo de rutas (config/routes.php)</label><br/>
		<input type='checkbox' name='scaffold_menu' id='scaffold_menu' value='<?php echo set_value('scaffold_menu', '1'); ?>' /> <label class='labelforminline' for="scaffold_menu">Modificar menú (views/partials/_menu.php)</label>

		<br/><br/>

		<input type='checkbox' checked name='create_controller' id='create_controller' value='<?php echo set_value('create_controller', '1'); ?>' /> <label class='labelforminline' for="create_controller">Crear controlador</label><br/>
		<input type='checkbox' checked name='create_model' id='create_model' value='<?php echo set_value('create_model', '1'); ?>' /> <label class='labelforminline' for="create_model">Crear modelo</label><br/>
		<input type='checkbox' checked name='create_view_create' id='create_view_create' value='<?php echo set_value('create_view_create', '1'); ?>' /> <label class='labelforminline' for="create_view_create">Crear vista 'crear'</label><br/>
		<input type='checkbox' checked name='create_view_list' id='create_view_list' value='<?php echo set_value('create_view_list', '1'); ?>' /> <label class='labelforminline' for="create_view_list">Crear vista 'lista'</label>
	</p>

	<p>
	    <?php echo form_submit( 'submit', 'Scaffold!',  "class='bcreateform'"); ?>
	</p>


	<?php echo form_close();?>

</div>


<div id='examples' style='width:300px;float:left;margin-left:30px;'>

<h3><a href="#">Text</a></h3>
<div>
<pre>
"name" :
{
  "type"			: 	"text",
  "minlength"		: 	"0",
  "maxlength"		: 	"60",
  "required"		: 	"FALSE",
  "multilanguage"	: 	"FALSE",
  "is_unique"		:	"FALSE"
}
</pre>
</div>

<h3><a href="#">Textarea</a></h3>
<div>
<pre>
"descripcion" 	:
{
  "type"			: "textarea",
  "minlength"		: "0",
  "maxlength"		: "500",			
  "required"		: "FALSE",
  "multilanguage"	: "FALSE",
  "ckeditor"		: "FALSE"
}
</pre>
</div>	

</div>

<div class='clear'></div>
