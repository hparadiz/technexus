{extends "/admin/design.tpl"}

{block "content"}
	

	<div class="row">
		<div class="col-11">
			<h5>Blog Posts</h5>
		</div>
		<div class="col-1">
		<a class="btn-sm btn-primary" href="/admin/posts/new"><i class="fas fa-edit"></i> New</a>
		</div>
	</div>
	<ul class="list-group">
		
		{foreach from=$data.BlogPosts item=BlogPost}
		<li class="list-group-item">
			{$BlogPost->Title}
			<div class="float-right">
				<span class="badge badge-secondary">{$BlogPost->Status}</span>
				<a class="btn-sm btn-secondary" href="/admin/posts/{$BlogPost->ID}/"><i class="fas fa-edit"></i> Edit</a>
				<a class="btn-sm btn-primary" href="/{date_format $BlogPost->Created "%Y"}/{date_format $BlogPost->Created "%m"}/{$BlogPost->Permalink}/"><i class="fas fa-eye"></i> View</a>
			</div>
		</li>
		{/foreach}
	</ul>
	</div>
{/block}