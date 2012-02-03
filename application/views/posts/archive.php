<h1>Archive</h1>

<?php if ($post): ?>
	
	<h3><?=$post->title ?></h3>
	<?=$post->content ?>
	<hr>

<?php else: ?>
	
	No hay posts

<?php endif ?>

<br>

<?php if ($tags): ?>

	<h3>Tags</h3>

	<?php foreach ($tags as $tag): ?>
		
		<?=$tag->name?> <br/>
		
	<?php endforeach ?>
	
<?php endif ?>

<br><br><br>

<?=anchor("users/articles/".$post->user_id, "Ver todos los posts de este usuario");?><br>

<?=anchor("", "Volver");?>