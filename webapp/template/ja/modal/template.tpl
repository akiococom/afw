<script>
	$(function() {
		// event setup
		
		// first setup
		$('#modal-meeting-form').modal('show');
		$('#modal-meeting-form').on('shown.bs.modal', function () {  });
		$('#modal-meeting-form').on('hide.bs.modal', function () {  });
	});
</script>
<div class="modal fade" id="modal-meeting-form" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success">{{'保存する'|lang}}</button>
			</div>
		</div>
	</div>
</div>