{extends "/blog/layouts/design.tpl"}

{block "title"}{$data.BlogPost->Title}  | {$dwoo.parent}{/block}

{block "description"}{substr(str_replace(array("\n","\t"),array(" "," "),trim(strip_tags($data.BlogPost->MainContent))),0,255)}{/block}

{block "content"}
	{load_templates "/blog/templates/post.tpl"}
	{BlogPost $data.BlogPost}
{/block}
