var editposts = {
	selectors: {
		textareaContent: 'textarea#mainContent',
		tagsEl: 'input[name="Tags"]',
		form: 'form'
	},
	onload: function (e)  {
		tinymce.init({
			selector: this.selectors.textareaContent,
			height: 400,
			init_instance_callback: function (editor) {
				editor.on('paste', function (event) {
					var items = (event.clipboardData || event.originalEvent.clipboardData).items;
					for (index in items) {
						var item = items[index];
						if (item.kind === 'file') {
							event.preventDefault();
							var blob = item.getAsFile();
							media.upload(blob);
						}
					}
				});
				editor.on('drop', function (event) {
					var items = event.dataTransfer.items;
					for (index in items) {
						var item = items[index];
						if (item.kind === 'file') {
							event.preventDefault();
							var blob = item.getAsFile();
							media.upload(blob);
						}
					}
				});
			},
			menubar: true, 
			content_css : [
				'/css/blog.css?'+new Date().getTime(),
				'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css',
				'https://fonts.googleapis.com/css?family=Playfair+Display:700,900',
				//'//cdnjs.cloudflare.com/ajax/libs/prism/1.20.0/themes/prism.css',
				'/css/prism-vsc-dark-plus.css',
				//'//cdnjs.cloudflare.com/ajax/libs/prism/1.20.0/themes/prism-twilight.min.css'
			],
			plugins: [
				'advlist autolink lists link image charmap print preview anchor ',
				'searchreplace visualblocks fullscreen',
				'insertdatetime media table code codesample wordcount save'
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
			toolbar: 'save | undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table | fontsizeselect',
			body_class: 'blog-main',
			extended_valid_elements: 'script[*]',
			relative_urls : false,
			remove_script_host : false,
			convert_urls : false,
			browser_spellcheck: true,
			smart_paste: false,
			paste_plaintext_inform: false,
			paste_data_images: false,
			paste_webkit_styles: "color font-size margin font font-variant-ligatures background-color",
			paste_retain_style_properties: "color font-size margin font font-variant-ligatures background-color",
			paste_word_valid_elements: 'style,strong/b,em/i,u,span,p,ol,ul,li,h1,h2,h3,h4,h5,h6,' +
				'p/div,a[href|name],sub,sup,strike,br,del,table[width],tr,' +
			    'td[colspan|rowspan|width],th[colspan|rowspan|width],thead,tfoot,tbody',
			paste_enable_default_filters: false,
			paste_remove_styles: false,
			paste_filter_drop: false,
			paste_remove_styles_if_webkit: false,
			valid_children : "+body[style]",
			save_enablewhendirty: false,
			save_onsavecallback: (e) => {
				tinyMCE.triggerSave();
				$(this.selectors.form).trigger('submit');
			},
		});

		$('input#title').on('change', (e) => {
			document.title = 'Editing '+e.currentTarget.value;
		});
		
		$(document).on('keydown', (e) => {
			if ((e.metaKey || e.ctrlKey) && ( String.fromCharCode(e.which).toLowerCase() === 's') ) {
				e.preventDefault();
				tinyMCE.triggerSave();
				$(this.selectors.form).trigger('submit');
			}
		});
			
		$( this.selectors.form ).on( 'submit', (e) => {
			e.preventDefault();
			this.save();
		});
		
		

		
		$(this.selectors.tagsEl).tagsinput({
		  tagClass: 'badge badge-pill badge-secondary',
		  freeInput: true,
		  trimValue: true,
		  typeahead: {
		    source: document.typeAheadTags,
		    afterSelect: () => {
		      $(this.selectors.tagsEl).tagsinput('input').val('');
		    }
		  }
		});
		
		// keeps the plugin from throwing a form.submit() on the main form
		$(this.selectors.tagsEl).on('itemAdded', (e) => {
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
