<div id="content-top">
    <h2><?=lang('web_list_user')?></h2>
    <a href='/admin/users/create/' class='bcreate'><?=lang('web_add_user')?></a>
    <a href='/admin/groups/' class='bcreategroup bforwardmargin'><?=lang('ggroups')?></a>	
    <span class="clearFix">&nbsp;</span>
</div>

<div class='clear'></div>


<table class='ftable' cellpadding="5" cellspacing="5">
	<thead>
		<th><?=lang('web_name')?></th>
		<th><?=lang('web_lastname')?></th>
		<th><?=lang('web_email')?></th>
		<th><?=lang('web_groups')?></th>
		<th>Status</th>
		<th colspan='2'><?=lang('web_options')?></th>
	</thead>
	<?php foreach ($users as $user):?>
		<tr>
			<td><?php echo $user->first_name;?></td>
			<td><?php echo $user->last_name;?></td>
			<td><?php echo $user->email;?></td>
			<td>
				<?php foreach ($user->groups as $group): ?>
					<?=$group->name."<br/>";?>
				<?php endforeach ?>
			</td>
			<td><?php echo ($user->active) ? "<a onClick=\"return confirm('".lang('web_confirm_deactivate')."')\" href='/admin/users/deactivate/".$user->id."'>".lang('web_active')."</a>" : "<a onClick=\"return confirm('".lang('web_confirm_active')."')\" href='/admin/users/activate/".$user->id."'>".lang('web_inactive')."</a>"?></td>
			<td width="60"><a class='ledit' href='/admin/users/edit/<?=$user->id?>/'><?=lang('web_edit')?></a></td>
			<td width="60"><a class='ldelete' onClick="return confirm('<?=lang('web_confirm_delete')?>')" href='/admin/users/delete/<?=$user->id?>/'><?=lang('web_delete')?></a></td>
		</tr>
	<?php endforeach;?>
</table>

	

