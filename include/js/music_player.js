
window.addEvent('load', function() {

	var swiffy = new FancyUpload2($('status'), $('list'), {
		'url': $('form1').action,
		'fieldName': 'file',
		'path': './images/music_Swiff.Uploader.swf',
		'onLoad': function() {
			$('status').removeClass('hide');
			$('fallback').destroy();
		}
	});

	/**
	 * Various interactions
	 */

	$('browse-files').addEvent('click', function() {
		swiffy.browse({'Music (*.mp3, *.mp4)': '*.mp3; *.mp4;'});
		return false;
	});

	$('clear-list').addEvent('click', function() {
		swiffy.removeFile();
		return false;
	});

	$('upload-now').addEvent('click', function() {
		swiffy.upload();
		return false;
	});

});