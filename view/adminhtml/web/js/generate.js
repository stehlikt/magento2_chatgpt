require([
    'jquery'
], function ($) {
    'use strict';
    $(document).on('click', '.generate-chatgpt-short-content', function () {
        var descriptionField = $(this).parent().parent().find('iframe').contents().find('body');
        var sku = $("input[name='product[sku]']").val();
        var type = 'short';
        if($(this).attr('id') == 'product_form_description_chatgpt' || controller == 'category') {
            type = 'full';
        }
        $.ajax({
            url: window.chatGptAjaxUrl,
            type: 'POST',
            showLoader: true,
            data: {
                'form_key': FORM_KEY,
                'sku': sku,
                'type': type,
                'controller': controller,
                'id': id
            },
            success: function(response) {
                if (response.error == false) {
                    var descriptionContent = '<p>' + response.data + '</p>';
                    descriptionField.html(descriptionContent).change();
                    descriptionField.focus();
                } else {
                    console.log(response.data);
                    alert({
                        title: $.mage.__('API Error'),
                        content: response.data
                    });
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    });

    $(document).on('click', 'button[data-index="generate_seo_description_button"], button[data-index="generate_seo_title_button"], .seo_button', function () {
        var sku = $("input[name='product[sku]']").val();
        if(controller == 'product')
        {
            if($(this).attr('data-index') == 'generate_seo_description_button')
            {
                var type = 'meta_description';
            }
            else
                var type = 'meta_title';
        }
        else
        {
            var type = $(this).attr('id');
        }
        $.ajax({
            url: window.chatGptAjaxUrl,
            type: 'POST',
            showLoader: true,
            data: {
                'form_key': FORM_KEY,
                'sku': sku,
                'controller': controller,
                'id': id,
                'type': type
            },
            success: function(response) {
                if (response.error == false) {
                    var descriptionContent = response.data;
                    if (type == 'meta_title') {
                        var inputSelector = (controller == 'product') ? 'input[name="product['+type+']"]' : 'input[name="'+type+'"]';
                        $(inputSelector).val(descriptionContent).focus().change();
                    } else {
                        var textareaSelector = (controller == 'product') ? 'textarea[name="product['+type+']"]' : 'textarea[name="'+type+'"]';
                        $(textareaSelector).val(descriptionContent).focus().change();
                    }

                } else {
                    console.log(response.data)
                    alert({
                        title: $.mage.__('API Error'),
                        content: response.data
                    });
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    });
    $(document).ready(function() {
        $('button.action-basic').removeClass('action-basic');
    });
});
