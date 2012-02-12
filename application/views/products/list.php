<div id="content-top">
    <h2>Listado de productos</h2>
    <a href='/products/create' class='bcreate'>Crear producto</a>
    <span class="clearFix">&nbsp;</span>
</div>

<div class='clear'></div>

<?php if ($products): ?>

	<table class='ftable' cellpadding="5" cellspacing="5">

		<thead>
			<th><?=lang('web_image')?></th>
			<th><?=lang('web_name')?></th>
			<th colspan='2'><?=lang('web_options')?></th>
		</thead>

		<tbody>
			<?php foreach ($products as $product): ?>
				
				<tr>
					<td><img src='/public/uploads/img/thumbs/<?=$product->image?>' /></td>
					<td><?=$product->name?> <br /></td>
					<td width="60"><a class='ledit' href='/products/edit/<?=$product->id?>'><?=lang('web_edit')?></a></td>
					<td width="60"><a class='ldelete' onClick="return confirm('<?=lang('web_confirm_delete')?>')" href='/products/delete/<?=$product->id?>'><?=lang('web_delete')?></a></td>
				</tr>
				
			<?php endforeach ?>
		</tbody>

	</table>

	<?php echo $links; ?>

<?php else: ?>

	<p class='text'><?=lang('web_no_elements');?></p>

<?php endif ?>