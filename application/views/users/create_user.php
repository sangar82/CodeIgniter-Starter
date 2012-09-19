<div id="content-top">
    <h2><?=($updType == 'create') ? lang('web_add_user') : lang('web_edit_user')?></h2>
    <a href='/admin/users/' class='bforward'><?=lang('web_back_to_list')?></a>
    <span class="clearFix">&nbsp;</span>
</div>

<?php 

$attributes = array('class' => 'tform', 'id' => '');
echo ($updType == 'create') ? form_open_multipart('admin/users/create', $attributes) : form_open_multipart('admin/users/edit', $attributes); 
?>

<p>
      <label class='labelform' for="first_name"><?=lang('web_name')?> <span class="required">*</span></label>
      <input id="first_name" type="text" name="first_name" maxlength="256" value="<?php echo set_value('first_name', (isset($user->first_name)) ? $user->first_name : ''); ?>"  />
      <?php echo form_error('first_name'); ?>
</p>

<p>
      <label class='labelform' for="last_name"><?=lang('web_lastname')?> <span class="required">*</span></label>
      <input id="last_name" type="text" name="last_name" maxlength="256" value="<?php echo set_value('last_name', (isset($user->last_name)) ? $user->last_name : ''); ?>"  />
      <?php echo form_error('last_name'); ?>
</p>

<p>
      <label class='labelform' for="email"><?=lang('web_email')?> <span class="required">*</span></label>
      <input id="email" type="text" name="email" maxlength="256" value="<?php echo set_value('email', (isset($user->email)) ? $user->email : ''); ?>"  />
      <?php echo form_error('email'); ?>
</p>

<p>
      <label class='labelform' for="password"><?=lang('web_password')?> <span class="required">*</span></label>
      <input id="password" type="password" name="password" maxlength="256" autocomplete="off" value="<?php echo set_value('password'); ?>"  />
      <?php echo form_error('password'); ?>
</p>

<p>
      <label class='labelform' for="password_confirm"><?=lang('web_password_confirm')?> <span class="required">*</span></label>
      <input id="password_confirm" type="password" name="password_confirm" autocomplete="off" maxlength="256" value="<?php echo set_value('password_confirm'); ?>"  />
      <?php echo form_error('password_confirm'); ?>
</p>

<?php if ($updType == 'edit'): ?>
      <?=form_hidden('id',$user->id) ?>
<?php endif ?>

<p>
    <?php echo form_submit( 'submit', ($updType == 'edit') ? lang('web_edit_user') : lang('web_add_user'), (($updType == 'create') ? "id='submit' class='bcreateform'" : "id='submit' class='beditform'")); ?>
</p>


<?php echo form_close();?>


