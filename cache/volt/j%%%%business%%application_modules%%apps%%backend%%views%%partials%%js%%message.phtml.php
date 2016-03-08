<?php if($this->view->auto_redirect){?>
	<script>
		var seconds = 3;
		var defaultUrl = "<?php echo $this->view->default_url;?>";

		$(document).ready(function(){
			if (defaultUrl == 'javascript:history.go(-1)' && window.history.length == 0)
			{
				document.getElementById('redirectionMsg').innerHTML = '';
				return;
			}
			window.setInterval(redirection, 1000);
		});

		function redirection()
		{
			if (seconds <= 0)
			{
				window.clearInterval();
				return;
			}
			seconds --;
			document.getElementById('spanSeconds').innerHTML = seconds;

			if (seconds == 0)
			{
				window.clearInterval();
				location.href = defaultUrl;
			}
		}
	</script>
	<?php }?>