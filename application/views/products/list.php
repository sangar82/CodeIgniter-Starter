<div id="content-top">
    <h2><?=lang('web_list_product')?></h2>
    <a href='/admin/products/create/<?= ($category_id != "")  ? $category_id : "0"?>/<?=$page?>' class='bcreate'>Crear producto</a>
    <?php if (isset($control)): ?>
    	<a href='/admin/categories/<?=$parent_category?>' class='bforward bforwardmargin'><?=lang('web_category_back')?></a>	
    <?php endif ?>
    
    <span class="clearFix">&nbsp;</span>
</div>

<div class='clear'></div>

<?php if ($products): ?>

	<table class='ftable' cellpadding="5" cellspacing="5">

		<thead>
			<th><?=lang('web_image')?></th>
			<th><?=lang('web_category')?></th>
			<th><?=lang('web_name')?></th>
			<th colspan='2'><?=lang('web_options')?></th>
		</thead>

		<tbody>
			<?php foreach ($products as $product): ?>
				
				<tr>
					<td><img src='/public/uploads/products/img/thumbs/<?=$product->image?>' /></td>
					<td><?=$product->category->name?> <br /></td>
					<td><?=$product->name?> <br /></td>
					<td width="60"><a class='ledit' href='/admin/products/edit/<?=$product->id?>/<?=($category_id != "") ? $category_id : "0" ?>/<?=$page?>'><?=lang('web_edit')?></a></td>
					<td width="60"><a class='ldelete' onClick="return confirm('<?=lang('web_confirm_delete')?>')" href='/admin/products/delete/<?=$product->id?>/<?=($category_id != "") ? $category_id : "0" ?>/<?=$page?>'><?=lang('web_delete')?></a></td>
				</tr>
				
			<?php endforeach ?>
		</tbody>

	</table>

	<?php echo $links; ?>

<?php else: ?>

	<p class='text'><?=lang('web_no_elements');?></p>

<?php endif ?>