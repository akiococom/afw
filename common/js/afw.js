var afw = afw || {};

$(function() {
	/* setup */
	
});
afw.putUploader = function(options) {
	var element = options.element ? options.element : '#div-dz-file';
	var formElement = options.formElement ? options.formElement : '#form';
	var formName = options.formName ? options.formName : 'files[]';
	var url = options.url ? options.url : 'upload';
	var maxFiles = options.maxFiles ? options.maxFiles : 10;
	var params = options.params ? options.params : {};
	var acceptedFiles = options.acceptedFiles ? options.acceptedFiles : '';
	var message = options.message ? options.message : 'ファイルをアップロード';
	var removeMessage = options.removeMessage ? options.removeMessage : 'キャンセル';
	var onSuccess = options.onSuccess ? options.onSuccess : false;
	
	$(element).removeClass('dropzone').addClass('dropzone').dropzone({
		url: url,
		parallelUploads: 1,
		maxFiles: maxFiles,
		params: params,
		acceptedFiles: acceptedFiles,
		dictDefaultMessage: '<i class="fas fa-cloud-upload-alt"></i> ' + message,
		success: function(_file, _response) {
			if (onSuccess) {
				if (!onSuccess(_file, _response)) {
					return;
				}
			}
			_response = $.parseJSON(_response);
			$(formElement).append(
				$('<input type="hidden" name="' + formName + '" />')
				.val(_response.response.filename + ':' + _response.response.server_filename)
			);
			_file.previewElement.classList.add('dz-success');
			$(_file.previewElement).attr('remove_key', _response.response.filename + ':' + _response.response.server_filename);
		},
		addRemoveLinks: true,
		dictRemoveFile: removeMessage,
		removedfile: function(file, e, f) {
			$('input').each(function() {
				if ($(this).val() == $(file.previewElement).attr('remove_key')) {
					$(this).remove();
				}
			});
			var _ref;
			return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
		}
	});
}

/**
 * common ajax loader
 *
 * action:		called ethna action
 * params:		get/post request parameter
 * element:		jquery element name of html view area.
 * callback:	callback function after loaded.
 * isLoading:	view loading spinner
 * 
 * #requried#
 * window.urlbase	
 * window.loadmethod	(default: post)
**/
afw.load = function(action, params, element, callback, isJSON) {
	var isSuccess = false;
	if (action) {
		if (typeof params == 'string') {
			params += '&action=' + action;
		} else {
			params['action'] = action;
		}
	}
	$.ajax({
		type: (window.loadmethod ? window.loadmethod : 'post'),
		url: window.urlbase,
		data: params,
		cache: false,
		dataType: (isJSON ? 'json' : 'html'),
		beforeSend: function() {
		},
		success: function(result) {
			if (result.success != false) {
				isSuccess = true;
			}
			if (element) {
				$(element).html(result);
			}
			if (callback) {
				callback(result);
			}
		},
		complete: function(result) {
			if (!isSuccess) {
				//alert(result.message);
			}
		},
        statusCode: {
        	403: function() {
        		///alert(window.afwMessageError);
        	},
            205: function() {
                window.location.reload();
            }
        }
	});
}

