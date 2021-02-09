var plant = plant || {};

$(function() {
	/* setup */
	
});

plant.setDatepicker = function() {
	/* calender */
	$.datepicker.setDefaults($.extend($.datepicker.regional['ja']));
	$('.datepicker').datepicker({ showAnim:'slideDown', dateFormat:'yy-mm-dd' });	
}

plant.getCaret = function(id) {
	var obj = $(id);
	obj.focus();
	if(navigator.userAgent.match(/MSIE/)) {
		//var r = document.selection.createRange();
		return $(id).val().length;
	} else {
		var s = obj.val();
		var p = obj.get(0).selectionStart;
		return p;
	}
}
/**
 * set tooltip
 */
plant.setTooltip = function(id) {
	$('#' + id).tooltip({'placement':'bottom'});
}

/**
 * put uploader
 * 
 * options: Object {
 * 		element
 * 		formElement*
 * 		url
 * 		maxFiles
 * 		acceptedFiles
 * }
 */
plant.putUploader = function(options) {
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
		dictDefaultMessage: '<i class="glyphicon glyphicon-file"></i> ' + message,
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
plant.load = function(action, params, element, callback, isLoading, isJSON) {
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
			if (isLoading) {
				plant.loading(false, element);
			}
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
			if (isLoading) {
				plant.loading(true, element);
			}
		},
        statusCode: {
        	403: function() {
        		///alert(window.plantMessageError);
        	},
            205: function() {
                window.location.reload();
            }
        }
	});
}

/**
 * check all checkbox
 */
plant.setCheckAll = function(element) {
	$('x-checks').find('.x-checked').each(function() {
		if ($(this).attr('check_ids')) {
			$(this).addClass('pointer');
			$(this).click(function() {
				$('.' + $(this).attr('check_ids')).each(function() {
					$(this).prop('checked', !$(this).prop('checked'));
				});
			});
		}
	});
}

/**
 * view loading layer
 * 
 * isRemove: remove loading layer
 */
plant.isLoading = false;
plant.loadingCount = 0;
plant._loadingOverlay = null;
plant.loading = function(isRemove, element) {
	isRemove = isRemove ? true : false;
	//var elm = '#div-loading';
	var elm = element || '#container';
	
	if (isRemove) {
		plant.loadingCount--;
		if (plant.loadingCount > 0) {
			return;
		}
	} else {
		plant.loadingCount++;
	}

	if (isRemove) {
		if (plant.loadingOverlay != null) {
			plant.loadingOverlay.remove();
		}
		plant.loadingOverlay = null
	} else if (plant.loadingOverlay == null) {
		var top  = Math.floor(($(window).height() - $(elm).height()) / 2);
		var left = Math.floor(($(window).width() - $(elm).width()) / 2);
		$(elm).css('top', top);
		$(elm).css('left', left);
		try {
			plant.loadingOverlay = getBusyOverlay("viewport", {
				color  : 'black',
				opacity: 0.1,
				text   : 'loading',
				style  : 'text-decoration:blink;font-weight:bold;font-size:12px;color:white;z-index:9999'
			}, {
				color: '#666',
				size : 100,
				type : 'c'
			});
		} catch(e) {}
	}
}

plant.isTel = function(str) {
	if (str.match(/[^0-9-]+/) && str.length < 8) {
		return false;
	}
	return true;
}
plant.isMail = function(str) {
	if (!str.match(/^[A-Za-z0-9]+[\w-]+@[\w\.-]+\.\w{2,}$/)) {
		return false;
	}
	return true;
}
plant.isRequired = function(str) {
	if (!str.trim().length) {
		return false;
	}
	return true;
}
plant.isLength = function(str, length) {
	if (str.trim().length < length) {
		return false;
	}
	return true;
}
plant.isAlphabet = function(str) {
	if (str.match(/[^A-Za-z0-9]+/)) {
		return false;
	}
	return true;
}

plant.isPassword = function(str) {
	if (str.match(/[^a-zA-Z0-9!-/:-@¥[-`{-~]+/)) {
		return false;
	}
	return true;
}

plant.isRequiredNumber = function(str) {
	if (parseInt(str) > 0) {
		return true;
	}
	return false;
}
