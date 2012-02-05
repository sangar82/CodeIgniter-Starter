<div class='message'>
<?
    $message = $this->session->flashdata('message');
    if($message)
    {
        if(is_array($message))
        {
            foreach($message as $line)
            {
                echo $line . '<br/>';
            }
        }
        else
        {
            echo $message;
        }
    }
?>
</div>