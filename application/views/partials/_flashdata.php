
<?php

    $message = $this->session->flashdata('message');

    //print_r($message);

    if($message)
    {

    	if ( is_array($message))
    	{
	        echo "<div class='msg_".$message['type']."'>";

	            echo "<span>".$message['text'] . "</span>";

	        echo "</div>";
	    }
    	else
    	{
	        echo "<div class='msg_success'>";

	            echo "<span>".$message . "</span>";

	        echo "</div>";
	    }



    }
?>
