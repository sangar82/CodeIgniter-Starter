<div id='content-top'>
    <h2><?=lang('web_list_group')?></h2>
   
    <a href='/admin/groups/create/<?=$page?>' class='bcreate'><?=lang('web_add_group')?></a>
    <a href='/admin/users/' class='bforward bforwardmargin'><?=lang('web_category_back')?></a>
  
    <span class='clearFix'>&nbsp;</span>
</div>

<?php if ($groups): ?>

<div class='clear'></div>

	<table class='ftable' cellpadding='5' cellspacing='5'>

		<thead>
			<th>Name</th>
			<th>Description</th>
			<th colspan='2'><?=lang('web_options')?></th>
		</thead>

		<tbody>
			<?php foreach ($groups as $group): ?>
				
				<tr>
					<td><?=$group->name;?></td>
					<td><?=$group->description;?></td>
					<td width='60'><a class='ledit' href='/admin/groups/edit/<?=$group->id?>/<?=$page?>'><?=lang('web_edit')?></a></td>
					<td width='60'><a class='ldelete' onClick="return confirm('<?=lang('web_confirm_delete')?>')" href='/admin/groups/delete/<?=$group->id?>/<?=$page?>'><?=lang('web_delete')?></a></td>
				</tr>
				
			<?php endforeach ?>
		</tbody>

	</table>

	<?php echo $links; ?>

<?php else: ?>

	<p class='text'><?=lang('web_no_elements');?></p>

<?php endif ?>