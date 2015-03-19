$(function() {
	function initTimestamps()
	{
		$('span.timestamp').each(function() {
			var $this = $(this);
			var date = new Date($this.data('timestamp'));
			$this.html(date.toLocaleString());
		});
	}
	initTimestamps();
	$(document).on('pjax:complete', initTimestamps);
});