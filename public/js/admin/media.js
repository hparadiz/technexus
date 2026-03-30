var adminmedia = {
	selectors: {
		notice: '#mediaNotice',
		loading: '#mediaLoading',
		empty: '#mediaEmpty',
		error: '#mediaError',
		fitToggle: '#mediaFitToggle',
		search: '#mediaSearch',
		grid: '#mediaGrid',
		template: '#mediaCardTemplate'
	},
	config: {},
	state: {
		filter: 'all',
		search: '',
		fitToScreen: false,
		compactIcons: false,
		allItems: [],
		invalidItems: []
	},
	onload: function () {
		this.loadConfig();
		this.bindEvents();
		this.loadMedia();
	},
	loadConfig: function () {
		var configScript = document.getElementById('mediaPageData');

		if (!configScript) {
			this.config = {};
			return;
		}

		try {
			this.config = JSON.parse(configScript.textContent);
		} catch (error) {
			this.config = {};
			this.showError('Could not load media page configuration.');
		}
	},
	bindEvents: function () {
		$(document).on('click', '[data-action="refresh-media"]', (e) => {
			e.preventDefault();
			this.loadMedia();
		});

		$(document).on('click', '[data-action="set-filter"]', (e) => {
			e.preventDefault();
			this.state.filter = e.currentTarget.getAttribute('data-filter') || 'all';
			this.syncFilterButtons();
			this.renderMedia();
		});

		$(document).on('click', '[data-action="toggle-fit"]', (e) => {
			e.preventDefault();
			this.state.fitToScreen = !this.state.fitToScreen;
			this.applyViewOptions();
		});

		$(document).on('click', '[data-action="toggle-icon-size"]', (e) => {
			e.preventDefault();
			this.state.compactIcons = !this.state.compactIcons;
			this.applyViewOptions();
		});

		$(this.selectors.search).on('input', (e) => {
			this.state.search = String(e.currentTarget.value || '').toLowerCase();
			this.renderMedia();
		});

		$(document).on('click', '[data-action="delete-media"]', (e) => {
			e.preventDefault();

			var button = e.currentTarget;
			var mediaId = button.getAttribute('data-media-id');

			if (!mediaId || !window.confirm('Delete media #' + mediaId + '?')) {
				return;
			}

			this.deleteMedia(mediaId, button);
		});
	},
	loadMedia: function () {
		this.hideError();
		this.setLoading(true);
		this.renderNotice();
		this.applyViewOptions();

		$.ajax({
			url: this.config.browseUrl,
			method: 'GET',
			dataType: 'json',
			success: (response) => {
				if (!response || response.success === false) {
					this.showError(this.getErrorMessage(response, 'Media failed to load.'));
					this.state.allItems = [];
					this.state.invalidItems = [];
					this.renderMedia();
					return;
				}

				this.inspectMedia(response.data || []);
			},
			error: () => {
				this.showError('Media failed to load.');
				this.state.allItems = [];
				this.state.invalidItems = [];
				this.renderMedia();
			},
			complete: () => {
				this.setLoading(false);
			}
		});
	},
	inspectMedia: function (items) {
		var checks = items.map((item) => this.inspectItem(item));

		Promise.all(checks).then((classifiedItems) => {
			this.state.allItems = classifiedItems;
			this.state.invalidItems = classifiedItems.filter((item) => item.isInvalid);
			this.renderMedia();
		}).catch(() => {
			this.showError('Media failed to load.');
			this.state.allItems = [];
			this.state.invalidItems = [];
			this.renderMedia();
		});
	},
	inspectItem: function (item) {
		item = $.extend({}, item);
		item.kind = this.getItemKind(item);
		item.isInvalid = false;
		item.invalidReason = '';

		if (item.kind !== 'image') {
			return Promise.resolve(item);
		}

		return new Promise((resolve) => {
			var probe = new Image();
			var finished = false;
			var finalize = function () {
				if (finished) {
					return;
				}

				finished = true;
				resolve(item);
			};

			probe.onload = finalize;
			probe.onerror = function () {
				item.isInvalid = true;
				item.kind = 'invalid';
				item.invalidReason = 'Thumbnail failed to load.';
				finalize();
			};
			probe.src = this.getThumbnailUrl(item);
		});
	},
	deleteMedia: function (mediaId, button) {
		var $button = $(button);
		var originalHtml = $button.html();

		$button.prop('disabled', true).text('Deleting...');

		$.ajax({
			url: this.config.deleteUrl + '/' + mediaId,
			method: 'POST',
			dataType: 'json',
			success: (response) => {
				if (!response || response.success === false) {
					this.showError(this.getErrorMessage(response, 'Delete failed.'));
					return;
				}

				this.config.notice = {
					type: 'success',
					message: 'Media deleted.'
				};

				this.showNotice(this.config.notice);

				this.loadMedia();
			},
			error: () => {
				this.showError('Delete failed.');
			},
			complete: () => {
				$button.prop('disabled', false).html(originalHtml);
			}
		});
	},
	renderMedia: function () {
		var items = this.getFilteredItems();
		var $grid = $(this.selectors.grid);
		var $empty = $(this.selectors.empty);

		$grid.empty();

		if (!items.length) {
			$empty.removeClass('d-none');
		} else {
			$empty.addClass('d-none');
		}

		items.forEach((item) => {
			$grid.append(this.renderCard(item));
		});
	},
	renderCard: function (item) {
		var template = $(this.selectors.template).html();
		var $card = $(template);
		var fileLabel = this.getFileLabel(item);
		var mimeType = item.MIMEType || 'application/octet-stream';
		var dimensions = item.Width && item.Height ? item.Width + 'x' + item.Height : '';
		var previewMode = this.getPreviewMode(item);

		$card.attr('data-media-id', item.ID);
		$card.find('[data-role="name"]').text(fileLabel);
		$card.find('[data-role="meta"]').text(this.buildMeta(item, mimeType, dimensions));
		$card.find('[data-role="open"]').attr('href', this.getOpenUrl(item));
		$card.find('[data-action="delete-media"]').attr('data-media-id', item.ID);

		if (item.isInvalid) {
			$card.addClass('media-card--invalid');
			$card.find('[data-role="thumb-image"]').remove();
			$card.find('[data-role="thumb-video"]').remove();
			$card.find('[data-role="thumb-fallback"]').remove();
			$card.find('[data-role="thumb-bad"]').removeClass('d-none');
		} else if (previewMode === 'image') {
			$card.find('[data-role="thumb-image"]')
				.attr('src', this.getThumbnailUrl(item))
				.attr('alt', fileLabel);
			$card.find('[data-role="thumb-video"]').remove();
			$card.find('[data-role="thumb-bad"]').remove();
		} else if (previewMode === 'video') {
			$card.find('[data-role="thumb-image"]').remove();
			$card.find('[data-role="thumb-video"]')
				.removeClass('d-none')
				.attr('src', this.getOpenUrl(item))
				.attr('aria-label', fileLabel)
				.prop('autoplay', true);
			$card.find('[data-role="thumb-bad"]').remove();
		} else {
			$card.find('[data-role="thumb-image"]').remove();
			$card.find('[data-role="thumb-video"]').remove();
			$card.find('[data-role="thumb-fallback"]').removeClass('d-none').text(mimeType);
			$card.find('[data-role="thumb-bad"]').remove();
		}

		return $card;
	},
	buildMeta: function (item, mimeType, dimensions) {
		var parts = ['#' + item.ID, mimeType];

		if (dimensions) {
			parts.push(dimensions);
		}

		if (item.Created) {
			parts.push(this.formatCreatedAt(item.Created));
		}

		return parts.join(' | ');
	},
	formatCreatedAt: function (createdAt) {
		var createdDate = this.parseCreatedAt(createdAt);

		if (!createdDate) {
			return String(createdAt);
		}

		return this.formatRelativeTime(createdDate);
	},
	parseCreatedAt: function (createdAt) {
		var numericValue;

		if (createdAt instanceof Date && !isNaN(createdAt.getTime())) {
			return createdAt;
		}

		if (typeof createdAt === 'number' || /^\d+$/.test(String(createdAt))) {
			numericValue = Number(createdAt);

			if (!isFinite(numericValue)) {
				return null;
			}

			if (numericValue < 1000000000000) {
				numericValue *= 1000;
			}

			return new Date(numericValue);
		}

		createdAt = new Date(createdAt);

		if (isNaN(createdAt.getTime())) {
			return null;
		}

		return createdAt;
	},
	formatRelativeTime: function (date) {
		var diffSeconds = Math.round((date.getTime() - Date.now()) / 1000);
		var absoluteSeconds = Math.abs(diffSeconds);
		var formatter = new Intl.RelativeTimeFormat('en', { numeric: 'auto' });

		if (absoluteSeconds < 60) {
			return formatter.format(diffSeconds, 'second');
		}

		if (absoluteSeconds < 3600) {
			return formatter.format(Math.round(diffSeconds / 60), 'minute');
		}

		if (absoluteSeconds < 86400) {
			return formatter.format(Math.round(diffSeconds / 3600), 'hour');
		}

		if (absoluteSeconds < 604800) {
			return formatter.format(Math.round(diffSeconds / 86400), 'day');
		}

		if (absoluteSeconds < 2629800) {
			return formatter.format(Math.round(diffSeconds / 604800), 'week');
		}

		if (absoluteSeconds < 31557600) {
			return formatter.format(Math.round(diffSeconds / 2629800), 'month');
		}

		return formatter.format(Math.round(diffSeconds / 31557600), 'year');
	},
	getFilteredItems: function () {
		return this.state.allItems.filter((item) => this.itemMatchesFilters(item));
	},
	itemMatchesFilters: function (item) {
		var filter = this.state.filter;
		var matchesSearch = this.matchesSearch(item);

		if (!matchesSearch) {
			return false;
		}

		switch (filter) {
			case 'images':
				return item.kind === 'image';
			case 'other':
				return item.kind === 'other' || item.kind === 'video';
			case 'invalid':
				return item.isInvalid;
			default:
				return true;
		}
	},
	matchesSearch: function (item) {
		var haystack;

		if (!this.state.search) {
			return true;
		}

		haystack = [
			item.ID,
			item.Caption,
			item.MIMEType,
			item.Created,
			item.invalidReason
		].join(' ').toLowerCase();

		return haystack.indexOf(this.state.search) !== -1;
	},
	getItemKind: function (item) {
		var mimeType = item.MIMEType || '';

		if (mimeType.indexOf('image/') === 0) {
			return 'image';
		}

		if (mimeType.indexOf('video/') === 0) {
			return 'video';
		}

		return 'other';
	},
	getPreviewMode: function (item) {
		if (item.kind === 'image') {
			return 'image';
		}

		if (item.kind === 'video') {
			return 'video';
		}

		return 'fallback';
	},
	getFileLabel: function (item) {
		if (item.Caption && String(item.Caption).trim()) {
			return item.Caption;
		}

		return 'Media #' + item.ID;
	},
	applyViewOptions: function () {
		$('body').toggleClass('media-fit-to-screen', this.state.fitToScreen);
		$(this.selectors.grid).toggleClass('media-grid--compact', this.state.compactIcons);
		$(this.selectors.fitToggle)
			.toggleClass('btn-dark', this.state.fitToScreen)
			.toggleClass('btn-outline-dark', !this.state.fitToScreen)
			.find('[data-role="label"]')
			.text(this.state.fitToScreen ? 'Narrow View' : 'Fit to Screen');
		$(this.selectors.fitToggle)
			.find('i')
			.attr('class', this.state.fitToScreen ? 'fas fa-compress-arrows-alt' : 'fas fa-expand-arrows-alt');
		$('[data-action="toggle-icon-size"]')
			.toggleClass('btn-dark', this.state.compactIcons)
			.toggleClass('btn-outline-dark', !this.state.compactIcons)
			.find('[data-role="label"]')
			.text(this.state.compactIcons ? 'Larger Icons' : 'Smaller Icons');
	},
	syncFilterButtons: function () {
		$('[data-action="set-filter"]').each((index, button) => {
			var $button = $(button);
			var isActive = $button.attr('data-filter') === this.state.filter;

			$button.toggleClass('active', isActive);
			$button.toggleClass('btn-dark', isActive);
			$button.toggleClass('btn-outline-dark', !isActive);
		});
	},
	getOpenUrl: function (item) {
		return '/media/open/' + item.ID;
	},
	getThumbnailUrl: function (item) {
		return '/media/thumbnail/' + item.ID + '/320x240xF5F1E8';
	},
	getErrorMessage: function (response, fallback) {
		if (response && response.failed && response.failed.errors) {
			return response.failed.errors;
		}

		return fallback;
	},
	setLoading: function (isLoading) {
		$(this.selectors.loading).toggleClass('d-none', !isLoading);
	},
	showNotice: function (notice) {
		var $notice = $(this.selectors.notice);

		if (!notice || !notice.message) {
			$notice.addClass('d-none').empty();
			return;
		}

		$notice
			.removeClass('d-none alert-success alert-danger alert-warning alert-info')
			.addClass('alert-' + (notice.type || 'info'))
			.text(notice.message);
	},
	renderNotice: function () {
		this.showNotice(this.config.notice);
	},
	showError: function (message) {
		$(this.selectors.error).removeClass('d-none').text(message);
	},
	hideError: function () {
		$(this.selectors.error).addClass('d-none').empty();
	}
};

$(document).ready(adminmedia.onload.bind(adminmedia));
