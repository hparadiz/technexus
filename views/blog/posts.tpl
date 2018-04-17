{extends "/blog/layouts/design.tpl"}

{block "title"}{if $data.Title}{$data.Title} | {$dwoo.parent}{else}{$dwoo.parent}{/if}{/block}

{block "content"}
	{load_templates "/blog/templates/post.tpl"}
	{foreach from=$data.BlogPosts item=BlogPost}
		{BlogPost $BlogPost}
	{/foreach}
	<nav class="blog-pagination">
		<a class="btn btn-outline-primary" href="#">Older</a>
		<a class="btn btn-outline-secondary disabled" href="#">Newer</a>
	</nav>
{/block}