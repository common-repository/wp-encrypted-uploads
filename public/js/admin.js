if (ANCENC !== undefined) {
    jQuery(document).ready($ => {
        const nonce = jQuery('#wp_ancenc_options_nonce').val();
        $('.ancenc-toggle').click(evt => {
            $(evt.target).siblings('input').trigger('click');
        });

        $('#wp-ancenc-save-settings').click((evt) => {
            evt.preventDefault();
            $('.ancenc-icon-loader').removeClass('ancenc-hidden');
            $.ajax(ANCENC.ajax_url, {
                method: 'POST',
                data: {
                    action: 'ancenc_update_settings',
                    nonce: nonce,
                    data: $('#wp-ancenc-settings-form').serialize()
                }
            }).then(data => {
                $("#ancenc-ajax-notice").toggle();
                $('.ancenc-icon-loader').addClass('ancenc-hidden');
                setTimeout(function () {
                    $("#ancenc-ajax-notice").toggle();
                }, 5000);
            })
        })
    })
}