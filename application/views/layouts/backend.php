<?php
$user = $this->ion_auth->user()->row();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

  <head>
  	<title><?=$title?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv='expires' content='1200' />
	<meta http-equiv='content-language' content='es' />
	<base href="<?php echo $this->config->item('base_url') ?>/public/" />
	<link rel="stylesheet" href="css/reset.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="css/bstyles.css" type="text/css" media="screen" />
	
</head>

<body>

	<div id='bcontainer'>

		<div id="bheader">


			<div id="btop">

			  <h1><a href="#">Cleanity</a></h1>
			  <p id="userbox"><?=lang('web_hello')?> <strong><?=$user->first_name?> <?=$user->last_name?></strong> &nbsp;| &nbsp;<a href="/auth/edit/<?=$user->id?>"><?=lang('web_my_account')?></a> &nbsp;| &nbsp;<a href="/logout"><?=lang('web_logout')?></a> <br>

			  <small><?=lang('web_lastlogin')?>: <?=date('d-M-Y H:i:s', $user->last_login)?></small></p>

			  <span class="clearFix">&nbsp;</span>

			</div>


			<ul id="bmenu">

				<?php  $mactive = ($this->uri->rsegment(1) == 'admin')  ? "class='selected'" : "" ?>
				<li <?=$mactive?>><a href="/admin/" style="background-position: 0px 0px;"><?=lang('web_home')?></a></li>

				<?php  $mactive = ($this->uri->rsegment(1) == 'auth')  ? "class='selected'" : "" ?>
				<li <?=$mactive?>><a href="/auth/" style="background-position: 0px 0px;"><?=lang('web_users')?></a></li>

				<?php  $mactive = ($this->uri->rsegment(1) == 'categories')  ? "class='selected'" : "" ?>
				<li <?=$mactive?> ><a href="/categories/" class="top-level" style="background-position: 0px 0px;"><?=lang('web_categories')?><span>&nbsp;</span></a></li>

				<?php  $mactive = ($this->uri->rsegment(1) == 'products')  ? "class='selected'" : "" ?>
				<li <?=$mactive?>><a href="/products/" style="background-position: 0px 0px;"><?=lang('web_products')?></a></li>

			</ul>


	      	<span class="clearFix">&nbsp;</span>

	    </div>	


	    <div id="bcontent">

			<?php $this->load->view("partials/_flashdata");?>
		
			<?=$body?>

			

		</div>



	</div>



	<div id="bfooter-wrap">

		<div id="bfooter">

	        <div id="bfooter-top">

	        	<div class="align-left">
		            <h4>Dashboard</h4>
		            <p><a href="#">Dasboard Sub 1</a> | <a href="#">Dasboard Sub 2</a> | <a href="#">Dasboard Sub 3</a></p>
	        	</div>

	            <div class="align-right">
	            	<h2><a href="#">Cleanity</a></h2>
	            </div>

	            <span class="clearFix"></span>
	            
	        </div><!-- end of div#footer-top -->
	        
	        <div id="bfooter-bottom">
	        	<p>&copy; 2009 Cleanity. Theme by Onur Oztaskiran of <a href="http://www.monofactor.com">Monofactor</a>, 
	            via <a href="http://themeforest.net">Themeforest</a></p>
    		</div>

		</div>

	</div>


	
</body>

</html>