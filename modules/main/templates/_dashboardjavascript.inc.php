<script type="text/javascript">
	TBG.Main.Dashboard.url = "<?php echo make_url("dashboard_view"); ?>";
	(function($) {
		var states = ["<?php
			echo image_url("collapse_small.png", false, "core", false);
		?>", "<?php
			echo image_url("expand_small.png", false, "core", false);
		?>"];
		$("#dashboard li").on("click", ".toggler", function() {
			this.src = this.src == states[0] ? states[1] : states[0];
			$("#dashboard_" + $(this).attr("data-id")).toggle();
		});
	})(jQuery);
</script>