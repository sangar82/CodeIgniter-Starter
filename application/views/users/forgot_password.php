<h1><?=lang('web_login_forgot')?></h1>

<p class='par'><?=lang('web_login_forgot_p')?></p>

<?php echo form_open("admin/users/forgot_password");?>

    <label>
      <strong class="email-label"><?=lang('web_email')?></strong><br>
      <?php echo form_input($email, set_value('email'), "class='forgot_input'");?>
      <?php echo form_error('email'); ?>
    </label>
      
      <br><br>
      <p><?php echo form_submit('submit', 'Submit', "id='submit'");?></p>
      
<?php echo form_close();?>