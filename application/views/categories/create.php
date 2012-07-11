<div id="content-top">
    <h2><?=lang(($updType == 'create') ? "web_add_category" : "web_edit_category")?></h2>
    <a href='/admin/categories/<?=$parent_id?>' class='bforward'><?=lang('web_back_to_list')?></a>
    <span class="clearFix">&nbsp;</span>
</div>


<?php 
$attributes = array('class' => 'tform', 'id' => '');
echo ($updType == 'create') ? form_open('/admin/categories/create', $attributes) : form_open('/admin/categories/edit', $attributes); 
?>

<p>
	<label class='labelform' for="name"><?=lang('web_name')?> <span class="required">*</span></label>
	<input id="name" type="text" name="name" maxlength="256" value="<?php echo set_value('name', (isset($category->name)) ? $category->name : ''); ?>"  />
	<?php echo form_error('name'); ?>
</p>

<p>
    <?php echo form_submit( 'submit', ($updType == 'edit') ? lang('web_category_edit') : lang('web_category_create'), (($updType == 'create') ? "id='submit' class='bcreateform'" : "id='submit' class='beditform'") ); ?>
</p>
	
	<?=form_hidden('parent_id',$parent_id) ?>	

<?php if ($updType == 'edit'): ?>
	<?=form_hidden('id',$category->id) ?>
<?php endif ?>

<?php echo form_close(); ?>