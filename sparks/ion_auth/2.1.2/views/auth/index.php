<div id="content-top">
    <h2><?=lang('web_list_user')?></h2>
    <a href='/auth/create' class='bcreate'><?=lang('web_add_user')?></a>
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
				<?php foreach ($user->groups as $group):?>
					<?php echo $group->name;?><br />
                <?php endforeach?>
			</td>
			<td><?php echo ($user->active) ? anchor("auth/deactivate/".$user->id, lang('web_active')) : anchor("auth/activate/". $user->id, lang('web_inactive'));?></td>
			<td width="60"><a class='ledit' href='/auth/edit/<?=$user->id?>'><?=lang('web_edit')?></a></td>
			<td width="60"><a class='ldelete' onClick="return confirm('<?=lang('web_confirm_delete')?>')" href='/auth/delete/<?=$user->id?>'><?=lang('web_delete')?></a></td>
		</tr>
	<?php endforeach;?>
</table>

	

