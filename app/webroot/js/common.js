(function () {
	var deleteLinkHandler = function (e) {
		e.preventDefault();

		var form = document.createElement('form');
		form.method = 'post';
		form.action = this.href;

		var input = document.createElement('input');
		input.type = 'hidden';
		input.name = '_method';
		input.value = 'DELETE';

		form.appendChild(input);

		document.getElementsByTagName('body')[0].appendChild(form);
		form.submit();
	};

	document.addEventListener('DOMContentLoaded', function () {
		var links = document.querySelectorAll('a.delete')
		for (var i = 0; i < links.length; i++) {
			links[i].addEventListener('click', deleteLinkHandler);
		}
	});
})();
