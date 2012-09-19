
<?php

    $message = $this->session->flashdata('message');

    if($message)
    {

    	if ( is_array($message['text']))
    	{
	        echo "<div class='msg_".$message['type']."'>";

                echo "<ul>";

                foreach ($message['text'] as $msg) {
                    echo "<li><span>".$msg."</span></li>";
                }

                echo "<ul>";

	        echo "</div>";
	    }
    	else
    	{
	        echo "<div class='msg_".$message['type']."'>";

	            echo "<span>".$message['text'] . "</span>";

	        echo "</div>";
	    }

    }
?>
