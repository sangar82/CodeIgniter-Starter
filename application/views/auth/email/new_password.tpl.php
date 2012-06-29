<html>
<body>
	<h1 style='font-family: lucida grande,tahoma,verdana,arial,sans-serif;font-size:16px;'><?=lang('npass_title')?></h1>

	<p style='font-family: lucida grande,tahoma,verdana,arial,sans-serif;font-size:13px;'><?=lang('npass_p1')?></p>

	<p style='font-family: lucida grande,tahoma,verdana,arial,sans-serif;font-size:13px;'>
		<b><?=lang('web_email')?>:</b> <?php echo $identity;?><br>
		<b><?=lang('npass_p2')?>:</b> <?php echo $new_password;?><br>
	</p>

	<p>
		<?=lang('npass_p3')?>
	</p>

	<p style='font-family: lucida grande,tahoma,verdana,arial,sans-serif;font-size:13px;'>
		<?=lang('confirm_email_thanks')?><br>
		<?=lang('confirm_email_team')?> <?=$this->config->item('site_title')?>
	</p>

</body>
</html>