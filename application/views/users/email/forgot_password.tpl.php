<html>
<body>
	<h1 style='font-family: lucida grande,tahoma,verdana,arial,sans-serif;font-size:16px;'><?=lang('changpass_title')?></h1>

	<p style='font-family: lucida grande,tahoma,verdana,arial,sans-serif;font-size:13px;'><?=lang('changpass_p1')?></p>

	<p style='font-family: lucida grande,tahoma,verdana,arial,sans-serif;font-size:13px;'>
		<?=lang('changpass_p2')?> <?php echo anchor('/reset_password/'. $forgotten_password_code, lang('changpass_rest'));?>		
	</p>

	<p style='font-family: lucida grande,tahoma,verdana,arial,sans-serif;font-size:13px;'>
		<?=lang('confirm_email_thanks')?><br>
		<?=lang('confirm_email_team')?> <?=$this->config->item('site_title')?>
	</p>

</body>
</html>