<div id='content-top'>
    <h2><?=($updType == 'create') ? lang('web_add_group') : lang('web_edit_group');?></h2>
    <a href='/admin/groups/<?=$page?>' class='bforward'><?=lang('web_back_to_list')?></a>
    <span class='clearFix'>&nbsp;</span>
</div>

<?php 
$attributes = array('class' => 'tform', 'id' => '');
echo ($updType == 'create') ? form_open_multipart('/admin/groups/create', $attributes) : form_open_multipart('/admin/groups/edit', $attributes); 
?>

<p>
	<label class='labelform' for='name'>Name <span class='required'>*</span></label>
	<input id='name' type='text' name='name' maxlength='60' value="<?php echo set_value('name', (isset($group->name)) ? $group->name : ''); ?>"  />
	<?php echo form_error('name'); ?>
</p>

<p>
	<label class='labelform' for='description'>Description </label>
	<textarea id="description"  name="description"  /><?php echo set_value('description', (isset($group->description)) ? htmlspecialchars_decode($group->description) : ''); ?></textarea>
	<?php echo form_error('description'); ?>
</p>

<p>
    <?php echo form_submit( 'submit', ($updType == 'edit') ? lang('web_edit') : lang('web_add'), (($updType == 'create') ? "id='submit' class='bcreateform'" : "id='submit' class='beditform'")); ?>
</p>

<?=form_hidden('page',set_value('page', $page)) ?>

<?php if ($updType == 'edit'): ?>
	<?=form_hidden('id',$group->id) ?>
<?php endif ?>

<?php echo form_close(); ?>