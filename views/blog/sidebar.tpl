<aside class="col-md-4 blog-sidebar">
  <div class="p-3 mb-3 bg-light rounded blog-sidebar-about">
    <h4 class="font-italic">About</h4>
    <p class="mb-0">I was playing with HTML, CSS, and JavaScript when IE6 was cutting edge browser technology. Since then I've learned a thing or two.</p>
    <p class="mb-0">Most of the things I write about here are really just references for myself and ways of collecting my thoughts on a topic. I do, however, hope what you find here is useful to you.</p>
  </div>

  <div class="p-3">
    <h4 class="font-italic">Archives</h4>
    <ol class="list-unstyled mb-0">
	  {foreach from=$data.Sidebar.Months item=Month}
	  	<li><a href="/blog/{$Month.Year}/{str_pad($Month.Month, 2, '0', 0)}/">{$Month.MonthName} {$Month.Year}</a></li>
	  {/foreach}
    </ol>
  </div>
  
  <div class="p-3">
	  <h4 class="font-italic">Topics</h4>
	  {foreach from=$data.Sidebar.Tags item=Tag}
	  	<a class="badge badge-secondary" href="/topics/{$Tag->Tag->Slug}">{$Tag->Tag->Tag}</a>	
	  {/foreach}
    </ol>
  </div>

  <div class="p-3">
    <h4 class="font-italic">Elsewhere</h4>
    <ol class="list-unstyled">
      <li><a href="https://github.com/hparadiz">GitHub</a></li>
      <li><a href="http://www.linkedin.com/in/henryparadiz">LinkedIn</a></li>
    </ol>
  </div>
</aside><!-- /.blog-sidebar -->