<div id="content-top">
    <h2><?=lang('web_list_deactivate_user')?></h2>
    <a href='/admin/users/' class='bcreate'><?=lang('web_back_to_list')?></a>
    <span class="clearFix">&nbsp;</span>
</div>

<p><?=lang('web_list_deuser_conf')?> '<?php echo $user->username; ?>'</p>
	
<?php echo form_open("users/deactivate/".$user->id);?>
	
  <p>

  	<label for="yes" class='labelforminline'><?=lang('web_yes')?>:</label>
      <input type="radio" id='yes' name="confirm" value="yes" checked="checked" />

    &nbsp;&nbsp;

  	<label for="no" class='labelforminline'><?=lang('web_no')?>:</label>
      <input type="radio" id='no' name="confirm" value="no" />

  </p>
  
  <?php echo form_hidden($csrf); ?>
  <?php echo form_hidden(array('id'=>$user->id)); ?>
  
  <p><?php echo form_submit('submit', 'Submit');?></p>

<?php echo form_close();?>


