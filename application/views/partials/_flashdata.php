
<?php

    $message = $this->session->flashdata('message');

    if($message)
    {

        echo "<div class='msg_".$message['type']."'>";

            echo "<span>".$message['text'] . "</span>";

        echo "</div>";

    }
?>
