{extends "/admin/design.tpl"}

{block "content"}
	<h5>Blog Posts</h5>
	<ul class="list-group">
		{foreach from=$data.BlogPosts item=BlogPost}
		<li class="list-group-item">
			{$BlogPost->Title}
			<div class="float-right">
				<a class="btn-sm btn-secondary" href="/admin/posts/{$BlogPost->ID}/"><i class="fas fa-edit"></i> Edit</a>
				<a class="btn-sm btn-primary" href="/{date_format $BlogPost->Created "%Y"}/{date_format $BlogPost->Created "%m"}/{$BlogPost->Permalink}/"><i class="fas fa-eye"></i> View</a>
			</div>
		</li>
		{/foreach}
	</ul>
{/block}