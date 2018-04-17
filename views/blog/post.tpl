{extends "/blog/layouts/design.tpl"}

{block "title"}{$data.BlogPost->Title}  | {$dwoo.parent}{/block}

{block "content"}
	{load_templates "/blog/templates/post.tpl"}
	{BlogPost $data.BlogPost}
{/block}
