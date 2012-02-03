<h1>All</h1>

<?php if ($all_posts): ?>
	
	<?php foreach ($all_posts as $post): ?>
		<h3><?=$post->title;?></h3>
		<p><?=$post->content;?></p>
		
		<a href='/posts/<?=url_title($post->title)?>/<?=$post->id?>'>ver</a>
		<hr />
	<?php endforeach ?>

	 <p><?php echo $links; ?></p>

	 <p><?=anchor("/posts/create", "Crear un nuevo post")?></p>
		
<?php else: ?>
	There aren´t new posts
<?php endif; ?>


<?php 
//$this->config->item('language');
//lang('site_intro');
?>