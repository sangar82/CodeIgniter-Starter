<div id="content-top">
    <h2><?=lang('web_category_list')?></h2>
    <a href='/categories/create' class='bcreate'><?=lang('web_add_category')?></a>
    <span class="clearFix">&nbsp;</span>
</div>

<div class='clear'></div>

<?php if ($categories): ?>

	<table class='ftable' cellpadding="5" cellspacing="5">

		<thead>
			<th><?=lang('web_name')?></th>
			<th colspan='2'><?=lang('web_options')?></th>
		</thead>

		<tbody>
			<?php foreach ($categories as $category): ?>
				
				<tr>
					<td><?=$category->name?> <br /></td>
					<td width="60"><a class='ledit' href='/categories/edit/<?=$category->id?>'><?=lang('web_edit')?></a></td>
					<td width="60"><a class='ldelete' onClick="return confirm('<?=lang('web_confirm_delete')?>')" href='/categories/delete/<?=$category->id?>'><?=lang('web_delete')?></a></td>
				</tr>
				
			<?php endforeach ?>
		</tbody>

	</table>

	<?php echo $links; ?>

<?php else: ?>

	<p class='text'><?=lang('web_no_elements');?></p

<?php endif ?>