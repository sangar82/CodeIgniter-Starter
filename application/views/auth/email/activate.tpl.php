<html>
<body>
	<h1 style='font-family: lucida grande,tahoma,verdana,arial,sans-serif;font-size:16px;'><?=lang('confirm_email_title')?></h1>

	<p style='font-family: lucida grande,tahoma,verdana,arial,sans-serif;font-size:13px;'><?=lang('confirm_email_0')?> <?=$this->config->item('site_title')?></p>

	<p style='font-family: lucida grande,tahoma,verdana,arial,sans-serif;font-size:13px;'>
		<?=lang('confirm_email_1')?>
		<br>
		<?php echo anchor('auth/activate/'. $id .'/'. $activation, lang('activate_account'));?>
		
	</p>

	<p style='font-family: lucida grande,tahoma,verdana,arial,sans-serif;font-size:13px;'>
		<?=lang('confirm_email_not_work')?><br>
		<?=$this->config->item('base_url').'/auth/activate/'. $id .'/'. $activation?>
	</p>
		
	<p style='font-family: lucida grande,tahoma,verdana,arial,sans-serif;font-size:13px;'><?=lang('confirm_email_48')?></p>

	<p style='font-family: lucida grande,tahoma,verdana,arial,sans-serif;font-size:13px;'>

		<?=lang('confirm_data_log')?><br><br>

		<b><?=lang('web_email')?>:</b> <?=$identity?><br>
		<b><?=lang('web_password')?>:</b> <?=$password?>

	</p>

	<br>

	<p style='font-family: lucida grande,tahoma,verdana,arial,sans-serif;font-size:13px;'>
		<?=lang('confirm_email_thanks')?><br>
		<?=lang('confirm_email_team')?> <?=$this->config->item('site_title')?>
	</p>

</body>
</html>