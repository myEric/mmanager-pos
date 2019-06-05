var Users = (function($){
	var showErrorMsg = function(form, type, msg) {
        var alert = $('<div class="kt-alert kt-alert--outline alert alert-' + type + ' alert-dismissible" role="alert">\
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\
			<span></span>\
		</div>');

        form.find('.alert').remove();
        alert.prependTo(form);
        //alert.animateClass('fadeIn animated');
        KTUtil.animateClass(alert[0], 'fadeIn animated');
        alert.find('span').html(msg);
    },
    handleUserLogin = function(){
		var options = {
			dataType: 'json',
			success: uerLoginServerResponse
		};
		$("#login-form").submit(function() {
			$(".btn-submit").attr("disabled","disabled");
			$(this).ajaxSubmit(options);	
			return false;
		});
	},
	uerLoginServerResponse = function(responseText, statusText, xhr, $form){
		if (responseText.success == true) {
			location.replace("/");
		}
	},
    handleSignInFormSubmit = function() {
        $('#kt_login_signin_submit').click(function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $(this).closest('form');           

            form.validate({
                rules: {
                    username: {
                        required: true
                    },
                    password: {
                        required: true
                    }
                }
            });

            if (!form.valid()) {
                return;
            }

            btn.addClass('kt-spinner kt-spinner--right kt-spinner--sm kt-spinner--light').attr('disabled', true);

            form.ajaxSubmit({
                url: $("#login-form").attr('action'),
                success: function(response, status, xhr, $form) {
                	if (response.success == true) {
                		location.replace(response.redirect);
                	} else {
                		btn.removeClass('kt-spinner kt-spinner--right kt-spinner--sm kt-spinner--light').attr('disabled', false);
                		showErrorMsg(form, 'danger', 'Incorrect username or password. Please try again.');
                	}
                }
            });
        });
    };
	return {
		init: function(){
			//handleUserLogin(),
			handleSignInFormSubmit()
		}
	}
})(jQuery);
jQuery(document).ready(function() {
    Users.init()
});
