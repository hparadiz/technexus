{template BlogPost BlogPost}
	<div class="blog-post">
		<h2 class="blog-post-title"><a href="/{date_format $BlogPost->Created "%Y"}/{date_format $BlogPost->Created "%m"}/{$BlogPost->Permalink}/">{$BlogPost->Title}</a></h2>
		<p class="blog-post-meta">
			{date_format $BlogPost->Created "%B %e, %Y"} by <a href="/about">Henry</a>{if \technexus\App::is_loggedin()}{/if}<span class="float-right">{if $BlogPost->Status=='Draft'}<span class="badge badge-secondary" title="Not visible to guests.">Draft</span>{/if}<a class="btn-sm btn" href="/admin/posts/{$BlogPost->ID}/"><i class="fas fa-edit"></i> Edit</a></span>
			{if $BlogPost->Tags}
				<br>
				<span class="blog-post-tags">
				{foreach from=$BlogPost->Tags item=Tag}
					<a class="badge badge-secondary" href="/topics/{$Tag->Tag->Slug}">{$Tag->Tag->Tag}</a>	
				{/foreach}
				</span>
			{/if}
		</p>
		<main>
		{$BlogPost->MainContent}
		</main>
	</div><!-- /.blog-post -->
{/template}