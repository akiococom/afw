<script>
	$(function() {
		// event setup
		
		// first load
		$('#modal-template').modal('show');
		$('#modal-template').on('hidden.bs.modal', function () {});
		$('#modal-template').on('shown.bs.modal', function () {});
	});
</script>
<div class="modal" id="modal-template" tabindex="-1" role="dialog" data-backdrop="static" data-aria-keyboard="false" >
	<div class="modal-dialog modal">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">{{''|lang}}</h5>
				<button type="button" class="close button-close-modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-warning size10" data-dismiss="modal">{{'閉じる'|lang}}</button>
			</div>
		</div>
	</div>
</div>