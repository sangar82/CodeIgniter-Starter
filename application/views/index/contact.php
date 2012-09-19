<?php 
$attributes = array('class' => 'tform', 'id' => '');
echo form_open('contact', $attributes);
?>

<h1><?=lang('web_contact')?></h1>

<p>
	<label class='labelform' for="name"><?=lang('web_name')?> <span class="required">*</span></label>
	<input id="name" type="text" name="name" maxlength="256" value="<?php echo set_value('name'); ?>"  />
	<?php echo form_error('name'); ?>
</p>

<p>
	<label class='labelform' for="lastname"><?=lang('web_lastname')?> <span class="required">*</span></label>
	<input id="lastname" type="text" name="lastname" maxlength="256" value="<?php echo set_value('lastname'); ?>"  />
	<?php echo form_error('lastname'); ?>
</p>

<p>
    <label class='labelform' for="email"><?=lang('web_email')?> <span class="required">*</span></label>
    <input id="email" type="text" name="email" maxlength="256" value="<?php echo set_value('email'); ?>"  />
    <?php echo form_error('email'); ?>
</p>

<p>
    <label class='labelform' for="phone"><?=lang('web_phone')?> <span class="required">*</span></label>
    <input id="phone" type="text" name="phone" maxlength="256" value="<?php echo set_value('phone'); ?>"  />
    <?php echo form_error('phone'); ?>
</p>

<p>
    <label class='labelform' for="comments"><?=lang('web_comments')?> <span class="required">*</span></label>
    <textarea name='comments' id='comments' cols='10' rows='6'><?php echo set_value('comments'); ?></textarea>
    <?php echo form_error('comments'); ?>
</p>

<p>
    <?php echo form_submit( 'submit', 'Enviar contacto'); ?>
</p>
	

<?php echo form_close(); ?>

