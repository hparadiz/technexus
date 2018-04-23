var editposts = {
	onload: function (e)  {
		tinymce.init({
			selector: 'textarea#mainContent',
			height: 400,
			menubar: true, 
			content_css : [
				'/css/blog.css?'+new Date().getTime(),
				'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css',
				'https://fonts.googleapis.com/css?family=Playfair+Display:700,900',
				'//cdnjs.cloudflare.com/ajax/libs/prism/1.13.0/themes/prism.css',
				'//cdnjs.cloudflare.com/ajax/libs/prism/1.13.0/themes/prism-twilight.min.css'
			],
			plugins: [
				'advlist autolink lists link image charmap print preview anchor',
				'searchreplace visualblocks fullscreen',
				'insertdatetime media table contextmenu code codesample wordcount'
			],
			codesample_languages: [
				{text: 'HTML/XML', value: 'markup'},
				{text: 'JavaScript', value: 'javascript'},
				{text: 'Bash', value: 'bash'},
				{text: 'CSS', value: 'css'},
				{text: 'PHP', value: 'php'},
				{text: 'Ruby', value: 'ruby'},
				{text: 'Python', value: 'python'},
				{text: 'Java', value: 'java'},
				{text: 'C', value: 'c'},
				{text: 'C#', value: 'csharp'},
				{text: 'C++', value: 'cpp'},
				{text: 'Smarty', value: 'smarty'},
				{text: 'Powershell', value: 'powershell'},
				{text: 'SQL', value: 'sql'},
				{text: 'Apache Config', value: 'apacheconf'}
			],
			body_class: 'blog-main',
			smart_paste: false,
			paste_plaintext_inform: false,
			paste_data_images: true,
			paste_webkit_styles: "color font-size margin font font-variant-ligatures background-color",
			paste_retain_style_properties: "color font-size margin font font-variant-ligatures background-color",
			paste_word_valid_elements: 'style,strong/b,em/i,u,span,p,ol,ul,li,h1,h2,h3,h4,h5,h6,' +
				'p/div,a[href|name],sub,sup,strike,br,del,table[width],tr,' +
			    'td[colspan|rowspan|width],th[colspan|rowspan|width],thead,tfoot,tbody',
			paste_enable_default_filters: false,
			paste_remove_styles: false,
			paste_filter_drop: false,
			paste_remove_styles_if_webkit: false,
			valid_children : "+body[style]"
		});
		
		$(document).on('keydown', (e) => {
			if ((e.metaKey || e.ctrlKey) && ( String.fromCharCode(e.which).toLowerCase() === 's') ) {
				console.log('save');
				e.preventDefault();
				this.save();
			}
		})
		
		
		$( 'form' ).on( 'submit', (e) => {
			console.log('butt');
			e.preventDefault();
			this.save();

		});
		
		
		var tagsEl = 'input[name="Tags"]';
		
		$(tagsEl).tagsinput({
		  tagClass: 'badge badge-pill badge-secondary',
		  freeInput: true,
		  trimValue: true,
		  typeahead: {
		    source: document.typeAheadTags,
		    afterSelect: () => {
		      $(tagsEl).tagsinput('input').val('');
		    }
		  }
		});
		
		// keeps the plugin from throwing a form.submit() on the main form
		$('input[name="Tags"]').on('itemAdded', (e) => {
			e.preventDefault();
		});
	},
	save: function() {
		$('.loader').show();
		
		var ID = $('#BlogPostID')[0].value;
	
		$.ajax({
			url: '/api/blogpost/json/' + ID + '/edit',
			method: 'POST',
			data: $( 'form' ).serialize(),
			success: (data,status,xhr) => {
				if(!data.success) {
					alert(data.failed.errors);
				}
				else {
					$('#lastEdit').html('Last edited ' + moment().format("MM/DD/YY h:mm A") );
				}
				$('.loader').hide();
			}
		});
	}
};

$(document).ready(editposts.onload.bind(editposts));