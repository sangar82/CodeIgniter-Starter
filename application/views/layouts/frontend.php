<?php
$user = $this->user;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

  <head>
  	<title><? echo $template['title'];?></title>
  	
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv='expires' content='1200' />
	<meta http-equiv='content-language' content='<?php echo $this->config->item('prefix_language') ?>' />
	<base href="<?php echo $this->config->item('base_url') ?>/public/" />
	<link rel="stylesheet" href="css/reset.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="css/fstyles.css?<?php echo time();?>" type="text/css" media="screen" />
	
</head>

<body>

	<div id='fcontainer'>

		<div id="fheader_right">

			<?php foreach ($this->config->item('languages') as $key => $value): ?>

				<?php if ($value == $this->config->item('language_default')): ?>
					<a href='http://www.<?=$this->config->item('base_domain')?>'><?=ucfirst($this->config->item('prefix_language_default'))?></a>&nbsp;
				<?php else: ?>
					<a href='http://<?=$key?>.<?=$this->config->item('base_domain')?>'><?=ucfirst($key)?></a>&nbsp;
				<?php endif ?>
				
			<?php endforeach ?>

		</div>


		<div id='fheader_left'>
			<?php if ($user): ?>
				<a href='/admin/'><?=lang('web_private_zone'); ?></a> | <a href='/logout/'>Salir</a>
			<?php else: ?>
				<a href='/login/'>Login</a>	
			<?php endif ?>
			
		</div>

		<br/><br/><br/><br/>


	    <div id="bcontent">

			<?php $this->load->view("partials/_flashdata");?>
		
			<?php echo $template['body']; ?>

		</div>

	</div>
	
</body>

</html>