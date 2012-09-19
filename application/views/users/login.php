

<br><br>

<div class="sign-in">

  <div class="signin-box">

    <h2><?=lang('web_login_access')?></h2>

    <?php echo form_open("admin/users/login");?>

    <label>
      <strong class="email-label"><?=lang('web_email')?></strong>
      <?php echo form_input($identity);?>
      <?php echo form_error('identity'); ?>
    </label>

    <label>
      <strong class="passwd-label"><?=lang('web_password')?></strong>
      <?php echo form_input($password);?> 
      <?php echo form_error('password'); ?>
    </label>

    <?php echo form_submit('submit', 'Login', "class='g-button g-button-submit'");?>
    
    <?php echo form_close();?>

    <ul>
      <li>
        <a target="_top" href="/forgot_password/" id="link-forgot-passwd">
          <?=lang('web_login_remb')?>
        </a>
      </li>
    </ul>

  </div>

</div>


