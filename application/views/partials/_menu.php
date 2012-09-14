<?php  $mactive = ($this->uri->rsegment(1) == 'admin')  ? "class='selected'" : "" ?>
<li <?=$mactive?>><a href="/admin/" style="background-position: 0px 0px;"><?=lang('web_home')?></a></li>

<?php  $mactive = ($this->uri->rsegment(1) == 'auth')  ? "class='selected'" : "" ?>
<li <?=$mactive?>><a href="/admin/users/" style="background-position: 0px 0px;"><?=lang('web_users')?></a></li>

<?php  $mactive = ($this->uri->rsegment(1) == 'categories')  ? "class='selected'" : "" ?>
<li <?=$mactive?>><a href="/admin/categories/" style="background-position: 0px 0px;">Categorias</a></li>

<?php  $mactive = ($this->uri->rsegment(1) == 'products')  ? "class='selected'" : "" ?>
<li <?=$mactive?>><a href="/admin/products/" style="background-position: 0px 0px;">Productos</a></li>