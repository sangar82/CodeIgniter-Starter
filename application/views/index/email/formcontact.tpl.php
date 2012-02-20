<html>
<body>
	<h1 style='font-family: lucida grande,tahoma,verdana,arial,sans-serif;font-size:16px;'>
		Formulario de contacto
	</h1>

	<p style='font-family: lucida grande,tahoma,verdana,arial,sans-serif;font-size:13px;'>
		Has recibido un nuevo email de contacto desde la página web
	</p>

	<p style='font-family: lucida grande,tahoma,verdana,arial,sans-serif;font-size:13px;'>
		<b>Nombre:</b><br/>			<?=$name?> <br/><br/>
		<b>Apellidos:</b><br/>		<?=$lastname?> <br/><br/>
		<b>Email:</b><br/>			<?=$email?> <br/><br/>
		<b>Teléfono:</b><br/>		<?=$phone?> <br/><br/>
		<b>Comentarios:</b><br/>	<?=$comments?> <br/><br/>
	</p>

	<p style='font-family: lucida grande,tahoma,verdana,arial,sans-serif;font-size:13px;'>
		No olvide ponerse en contacto lo antes posible con este contacto.
	</p>

	<br>

	<p style='font-family: lucida grande,tahoma,verdana,arial,sans-serif;font-size:13px;'>
		<?=lang('confirm_email_thanks')?><br>
		<?=lang('confirm_email_team')?> <?=$this->config->item('site_title', 'ion_auth')?>
	</p>

</body>
</html>