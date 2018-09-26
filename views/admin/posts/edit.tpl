{extends "/admin/design.tpl"}

{block "title"}Editing {if $data.BlogPost->Title}{$data.BlogPost->Title}{else}$data.BlogPost->ID}{/if}{/block}

{block "meta-top"}
	{$dwoo.parent}
	<script src="//cdnjs.cloudflare.com/ajax/libs/tinymce/4.7.10/tinymce.min.js" integrity="sha256-ibVg3nLs0DX10T7r/GxKZzdLsqyGTDzVBQv9WmxQOt0=" crossorigin="anonymous"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.0/moment.min.js" integrity="sha256-DELCOgoVsZqjT78lDC7zcX+YFp+PEjh1k23mBMoDBwo=" crossorigin="anonymous"></script>
	<link href="//www.tinymce.com/css/codepen.min.css" rel="stylesheet">
	<link href="/css/editor.css?{time()}" rel="stylesheet">
{/block}

{block "content"}
	<div class="col-md-9 order-md-1">
		<h4 class="mb-3">Editing Blog Post (ID: {$data.BlogPost->ID})</h4>
		<input type="hidden" id="BlogPostID" value="{$data.BlogPost->ID}">
		<form>
			<div class="mb-3">
				<label for="title">Title</label>
				<input type="text" class="form-control" id="title" placeholder="" name="Title" value="{$data.BlogPost->Title}" required="" spellcheck="true">
			</div>
			<div class="mb-3">
				<label for="title">Permalink</label>
				<input type="text" class="form-control" id="title" placeholder="" name="Permalink" value="{$data.BlogPost->Permalink}" required="" spellcheck="true">
			</div>
			<div class="mb-3">
				<label for="mainContent">Content</label>
				<textarea class="form-control" id="mainContent" placeholder="" name="MainContent" spellcheck="true">{$data.BlogPost->MainContent}</textarea>
			</div>
			<div class="mb-3">
				<label for="status">Status</label>
				<select class="form-control" id="status" name="Status">
					<option{if $data.BlogPost->Status == 'Draft'} selected{/if}>Draft</option>
					<option{if $data.BlogPost->Status == 'Published'} selected{/if}>Published</option>
				</select>
			</div>
			<div class="mb-3">
				<label for="tags">Tags</label>
				<input type="text" class="form-control" name="Tags" value="{$data.BlogPost->getTags()}">
			</div>
			<small id="lastEdit" class="form-text text-muted">Last edited {date_format $data.BlogPost->Edited "%m/%d/%y %k:%M %p"}<div class="loader float-right" id="loader-1" style="display: none;"></div></small>
			<hr class="mb-4">
			<button class="btn btn-primary btn-lg btn-block" type="submit">Save</button>
		</form>
	</div>
	<script>
		document.typeAheadTags = [{\technexus\Models\Tag::getTypeahead()}];
	</script>
{/block}

{block "js-bottom"}
	{$dwoo.parent}
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js" integrity="sha256-LOnFraxKlOhESwdU/dX+K0GArwymUDups0czPWLEg4E=" crossorigin="anonymous"></script>
	<script src="/js/bootstrap-tagsinput/bootstrap-tagsinput.js?{time()}"></script>
	<link href="/js/bootstrap-tagsinput/bootstrap-tagsinput.css?{time()}" rel="stylesheet">
	<script src="/js/admin/posts/edit.js?{time()}" rel="stylesheet"></script>
	<script src="/js/media.js?{time()}" rel="stylesheet"></script>
{/block}