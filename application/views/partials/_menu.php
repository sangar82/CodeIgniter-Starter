<?php  $mactive = ($this->uri->rsegment(1) == 'admin')  ? "class='selected'" : "" ?>
<li <?=$mactive?>><a href="/admin/" style="background-position: 0px 0px;"><?=lang('web_home')?></a></li>

<?php  $mactive = ($this->uri->rsegment(1) == 'auth')  ? "class='selected'" : "" ?>
<li <?=$mactive?>><a href="/auth/" style="background-position: 0px 0px;"><?=lang('web_users')?></a></li>

<?php  $mactive = ($this->uri->rsegment(1) == 'categories')  ? "class='selected'" : "" ?>
<li <?=$mactive?> ><a href="/categories/" class="top-level" style="background-position: 0px 0px;"><?=lang('web_categories')?><span>&nbsp;</span></a></li>
