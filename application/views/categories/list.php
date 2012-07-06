<div id="content-top">
    <h2><?=lang('web_category_list')?></h2>
    <a href='/categories/create/<?=$this->uri->segment(2)?>' class='bcreate'><?=lang('web_add_category')?></a>
    <span class="clearFix">&nbsp;</span>
    <?php


if ($category_id){

	$nav_home =  "<a href='/categories/'>".lang('web_home')."</a> &nbsp;&nbsp;>&nbsp;&nbsp; ";
	$nav =  $category->name;
	
	while(! is_null($category->category ) )
	{
		$nav = "<a href='/categories/".$category->category->id."'>".$category->category->name . "</a> &nbsp;&nbsp;>&nbsp;&nbsp; ". $nav; 

		$category = $category->category;
	}

	echo "<div id='nav_categories'>$nav_home $nav</div>";
}
?>
</div>

<div class='clear'></div>







<?php if ($categories): ?>

	<table id='tcategories' class='ftable' cellpadding="5" cellspacing="5">

		<thead>
			<th></th>
			<th><?=lang('web_name')?></th>
			<th>Productos</th>
			<th colspan='2'><?=lang('web_options')?></th>
		</thead>

		<tbody>
			<?php foreach ($categories as $category): ?>
				
				<tr id='<?=$category->id?>'>
					<td valign='middle'><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></td>
					<td width='150'><a href='/categories/<?=$category->id?>'><?=$category->name?></a> (<?= ($category->categories) ?  count($category->categories) :  "0" ?>)</td>
					<td width='370'><a href='/products/product_list/<?=$category->id?>/1'>Listado de productos</a> (<?= ($category->products) ?  count($category->products) :  "0" ?>)</td>
					<td width="60"><a class='ledit' href='/categories/edit/<?=$category->id?>/<?=($category->category) ? $category->category->id : "" ?>'><?=lang('web_edit')?></a></td>
					<td width="60"><a class='ldelete' onClick="return confirm('<?=lang('web_confirm_delete')?>')" href='/categories/delete/<?=$category->id?>'><?=lang('web_delete')?></a></td>
				</tr>
				
			<?php endforeach ?>
		</tbody>

	</table>

<?php else: ?>

	<p class='text'><?=lang('web_no_elements');?></p>

<?php endif ?>