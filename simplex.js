$('#build-button').click(function() {
	$('#loading').show(600);

	var size = parseInt($('#select-size').val());
	var filter = $('#select-filter').val();
	var sampleCount = parseInt($('#select-samples').val());
	sampleCount += Math.log(size) / Math.LN2 - 4;
	
	var seed = $('#input-seed').val();
	
	var data = { };
	data['size'] = size;
	data['filter'] = filter;
	data['samples'] = sampleCount;
	if(seed != '')
		data['seed'] = seed;
	
	$.get('img.php', data, function(data) {
		$('#loading').hide(600);
		$('#image').hide(400, function() {
			$('#image').html('<img src="data:image/png;base64,' + data + '"></img>');
			$('#image').show(400);
		});
	});
});