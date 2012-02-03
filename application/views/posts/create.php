<h1>Crear posts</h1>

<?php // Change the css classes to suit your needs    

echo validation_errors();

$attributes = array('class' => '', 'id' => '');
echo form_open('posts/create', $attributes); ?>

<p>
        <label for="title">Title: <span class="required">*</span></label>
        <br /><input id="title" type="text" name="title" maxlength="256" value="<?php echo set_value('title'); ?>"  />
        <?php echo form_error('title'); ?>
</p>

<p>
        <label for="content">Content: <span class="required">*</span></label>
        <br /><input id="content" type="text" name="content" maxlength="256" value="<?php echo set_value('content'); ?>"  />
        <?php echo form_error('content'); ?>
</p>

<p>
        <label for="user_id">User id:</label>
        <br /><input id="user_id" type="text" name="user_id"  value="<?php echo set_value('content'); ?>"  />
        <?php echo form_error('user_id'); ?>
</p>


<p>
        <?php echo form_submit( 'submit', 'Submit'); ?>
</p>

<?php echo form_close(); ?>