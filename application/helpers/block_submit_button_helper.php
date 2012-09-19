<?php
/**
* Anchor Link with Translations
*
* Return the script to block the webpage to prevent double clicks when you submit a form
*
* @access	public
* @param	void	
* @return	string
*/	
function block_submit_button()
{
	$CI =& get_instance();

	$data = "<script src=\"js/jquery.blockUI.js\" type=\"text/javascript\"></script>

	<script>

	loadImage = new Image();
	loadImage.src = \"".$CI->config->item('base_url')."/public/img/admin/ajax-loader.gif\";
				
	$(document).ready(function() { 

	    $('#submit').click(function() { 

		$.blockUI({ 
			message: \"<div class=ajaxloader_text>".lang('web_wait')."</div>\",
			timeout: 120000
		}); 	
	    
	    }); 
	});

	</script>";

	return $data;
}
?>