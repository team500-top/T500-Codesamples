/**
 * WPSEODream JavaScript functionality
 */
jQuery(document).ready(function($) {
    var $titleInput = $('#WPSEODream_title'),
		$descriptionArea = $('#WPSEODream_description');
		
	$titleInput.keyup(function () {
		$('.WPSEODream-preview-title').text($titleInput.val());
	});
	
	$descriptionArea.keyup(function () {
		$('.WPSEODream-preview-description').text($descriptionArea.val());
	});
	
	if (window.location.href.indexOf('tab=automatic-titles') !== -1) {
		$('.nav-tab').removeClass('nav-tab-active');
		$('.automatic-titles-tab-button').addClass('nav-tab-active');
		$('.WPSEODream-settings-tab').hide();
		$('#automatic-titles').show();
	}
	else if (window.location.href.indexOf('tab=sitemap') !== -1) {
		$('.nav-tab').removeClass('nav-tab-active');
		$('.sitemaps-tab-button').addClass('nav-tab-active');
		$('.WPSEODream-settings-tab').hide();
		$('#sitemaps').show();
	}
	else if (window.location.href.indexOf('tab=advanced') !== -1) {
		$('.nav-tab').removeClass('nav-tab-active');
		$('.advanced-tab-button').addClass('nav-tab-active');
		$('.WPSEODream-settings-tab').hide();
		$('#advanced').show();
	}
	else {
		$('.nav-tab').removeClass('nav-tab-active');
		$('.main-settings-tab-button').addClass('nav-tab-active');
		$('.WPSEODream-settings-tab').hide();
		$('#main-settings').show();		
	}
	
	$('.WPSEODream-settings-wrap').show();
	
	console.log('WPSEODream loaded.');
});