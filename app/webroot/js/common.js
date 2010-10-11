$(document).ready(function() {
	$('a.delete').click(function(){
		if (confirm('Are you sure you want to delete this?')) {
			$('<form method="post"></form>')
				.attr('action', this.href)
				.appendTo('body')
				.submit();
			return false;
		}

		return false;
	});
});
