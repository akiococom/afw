{{* スクリプト読み込み *}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
<script type="text/javascript" src="{{$config.base}}common/js/afw.js"></script>

{{* Dropzone *}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
<script>
	Dropzone.autoDiscover = false;
	$('.a-location-back').on('click', function(e) {
		e.preventDefault();
		history.back();
	});
</script>

{{* Google Analytics *}}
{{if $config.google_analytics}}
	<script async src="https://www.googletagmanager.com/gtag/js?id={{$config.google_analytics}}"></script>
	<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());

	gtag('config', '{{$config.google_analytics}}');
	</script>
{{/if}}

{{script}}
	$(document).on('click', '.a-remove', function(e) {
		if (confirm('{{'削除してもよろしいですか？'|lang}}')) {
			return true;
		} else {
			e.preventDefault();
			return false;
		}
	});
{{/script}}
{{script}}
	{{* ログイン情報をlocalStorageに *}}
	{{if $session.user_id}}
		{{if $session.is_persistent}}
			window.localStorage.setItem('session_id', '{{$session.user_id}}');
			window.localStorage.setItem('session_token', '{{$app.session_token}}');
		{{/if}}
	{{else}}
		if (window.localStorage.getItem('session_id')) {
			var params = {
				session_id: window.localStorage.getItem('session_id'),
				session_token: window.localStorage.getItem('session_token')
			}
			var callback = function(r) {
				if (r.success) {
					location.reload();
				}
			}
			plantfc.load('sign_in_storage_accept', params, false, callback, false, true);
		}
	{{/if}}
	
	$.fn.extend({
		insertAtCaret: function(v) {
			var o = this.get(0);
			o.focus();
			if (navigator.userAgent.match(/MSIE/)) {
				var r = document.selection.createRange();
				r.text = v;
				r.select();
			} else {
				var s = o.value;
				var p = o.selectionStart;
				var np = p + v.length;
				o.value = s.substr(0, p) + v + s.substr(p);
				o.setSelectionRange(np, np);
			}
		}
	});	
{{/script}}