$(document).ready(function() {
	$('a.delete').click(function(){
		if (confirm('Are you sure you want to delete this?')) {
			$('<form method="post"><input type="hidden" name="_method" value="DELETE"/></form>')
				.attr('action', this.href)
				.appendTo('body')
				.submit();
			return false;
		}

		return false;
	});
});
