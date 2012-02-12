<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

  <head>
  	<title><?=$title?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv='expires' content='1200' />
	<meta http-equiv='content-language' content='es' />
	<base href="<?php echo $this->config->item('base_url') ?>/public/" />
	<link rel="stylesheet" href="css/fstyles.css" type="text/css" media="screen" />
</head>

<body>
	
	<?php $this->load->view("partials/_flashdata");?>

	<?=$body?>
	
</body>

</html>