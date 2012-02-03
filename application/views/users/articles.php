<h1>Articles by Author</h1>

<h2>Articles by <?=$posts[0]->user->name;?></h2>

<?php if ($posts): ?>
	
	<?php foreach ($posts as $post): ?>

		<h3><?=$post->title;?></h3>
		<p><?=$post->content;?></p>

		<?php if ($post->tags): ?>

			Tags: 
			<?php foreach ($post->tags as $tags): ?>
				<?=$tags->name.",";?>		
			<?php endforeach ?>
					
		<?php endif ?>
	
		<hr>
		
	<?php endforeach ?>

<?php endif ?>

<?=anchor("", "Volver");?>