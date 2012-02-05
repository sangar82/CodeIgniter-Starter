<h1>Crear usuarios</h1>

<?php // Change the css classes to suit your needs    

//echo validation_errors();

$attributes = array('class' => '', 'id' => '');
echo form_open_multipart('users/create', $attributes); ?>


<p>
        <label for="name">Nombre: <span class="required">*</span></label>
        <br /><input id="name" type="text" name="name" maxlength="256" value="<?php echo set_value('name'); ?>"  />
        <?php echo form_error('name'); ?>
</p>

<p>
        <label for="lastname">Apellidos: <span class="required">*</span></label>
        <br /><input id="lastname" type="text" name="lastname" maxlength="256" value="<?php echo set_value('lastname'); ?>"  />
        <?php echo form_error('lastname'); ?>
</p>

<p>
        <label for="email">Email:</label>
        <br /><input id="email" type="text" name="email"  value="<?php echo set_value('email'); ?>"  />
        <?php echo form_error('email'); ?>
</p>
<p>
        <label for="image">Imagen:</label>
        <br /><?= form_upload('image') ?>
        <br />
        <?php  echo ( isset($upload_error)) ?  $upload_error  : ""; ?>
        
</p>


<p>
        <?php echo form_submit( 'submit', 'Submit'); ?>
</p>

<?php echo form_close(); ?>