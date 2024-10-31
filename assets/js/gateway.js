'use strict'
jQuery( document ).ready(
	function () {
		(function ($, settings) {
			var showSpinner = function () {
				var htmlString =
				"<div class='" +
				settings.spinner_id +
				"'><div class='" +
				settings.spinner_id +
				"-center'><span class='spinner is-active'><img src='" +
				settings.spinner_url +
				"'></span></div></div>"
				htmlString     =
				htmlString +
				'<style> .' +
				settings.spinner_id +
				'{z-index:99999;width: 100%;height: 100%;position: fixed;top: 0;left: 0;opacity: 0.4;background-color:#ccc;text-align: center; } .' +
				settings.spinner_id +
				'-center{position: absolute;top: 50%; left: 50%; transform: translate(-50%, -50%); } .' +
				settings.spinner_id +
				' .spinner{ vertical-align: middle; }' +
				settings.spinner_id +
				' * img{z-index:99999;}</style>'

				$( 'body' ).prepend( htmlString )
			}

			var removeSpinner = function () {
				jQuery( '.' + settings.spinner_id ).remove()
			}

			var cta = function () {
				showSpinner()

				var dataToSend = {
					action: settings.action,
					order_id: settings.order_id,
					nonce: settings.ajax_nonce,
				}

				$.post(
					settings.ajax_url,
					dataToSend,
					function (response) {
						if (response.success) {
							var options = {
								qrString: response.data.qr,
								checkoutId: response.data.id,
								deeplink: {
									url: response.data.deeplink,
									callbackURL: settings.modalCallbackURL,
									callbackURLSuccess: settings.modalCallbackURLSuccess,
								},
								onSuccess: function () {
									console.log( 'onSuccess' )
								},
								onFailure: function () {
									console.log( 'onFailure' )
								},
								onCancel: function () {
									console.log( 'onCancel' )
								},
								refreshData: function ()
								{
									return new Promise(function (resolve, reject) {
										$.ajax({
											url: settings.ajax_url,
											async: true,
											data: dataToSend,
											method: 'POST',
											success: function (data) {
												resolve({
													qrString: data.data.qr,
													checkoutId: data.data.id,
													deeplink: {
														url: response.data.deeplink,
														callbackURL: settings.modalCallbackURL,
														callbackURLSuccess: settings.modalCallbackURLSuccess,
													}
												});
											}
										});
									});
								},
								callbackURL: settings.modalCallbackURLSuccess,
							}

							ModoSDK.modoInitPayment( options )
						} else {
							console.log( 'onFailurePost' )
							console.log( response )
						}
						removeSpinner()
					}
				)
			}

			$( '#modo-modal-cta' ).click(
				function () {
					cta()
				}
			)
			if(settings.modo_cta_flag){
				cta()
			}
		})( jQuery, wc_modo_settings )
	}
)
