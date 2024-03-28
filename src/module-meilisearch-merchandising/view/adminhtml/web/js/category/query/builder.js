define([
    'jquery',
    'queryBuilder',
    'Magento_Ui/js/modal/modal',
    'mage/template'
], function($, queryBuilder, modal, template) {
    return function(config, element) {
        let qb = $(config.queryBuilderContainer).queryBuilder({
            filters: config.filters
        });

        $(element).submit(function(e) {
            e.preventDefault();
            let rules = qb.queryBuilder('getRules');
            let categoryId = $('#category_id').val();

            if (rules && rules.valid && categoryId > 0) {
                $.ajax(this.action, {
                    showLoader: true,
                    dataType: 'json',
                    data: {
                        form_key: window.FORM_KEY,
                        rules: rules,
                        category_id: categoryId,
                        storeId: config.storeId
                    }
                });
            }
        });

        $('#apply-rule').on('click', function() {
            let rules = qb.queryBuilder('getRules');

            if (rules && rules.valid) {
                $.ajax(config.previewUrl, {
                    showLoader: true,
                    dataType: 'json',
                    data: {
                        form_key: window.FORM_KEY,
                        rules: rules,
                        storeId: config.storeId
                    },
                    success: function(response) {
                        let previewTemplate = template('#category-merchandising-preview-template');

                        $('#category-merchandising-preview').empty();

                        $.each(response.hits, function(i, v) {
                            let preview = previewTemplate({
                                data: { ...v }
                            });
                            $('#category-merchandising-preview').append(preview);
                        });
                    }
                });
            }
        });

        qb.on('afterSetRules.queryBuilder', function() {
            $('#apply-rule').click();
        });

        let options = {
            type: 'slide',
            responsive: true,
            title: 'Main title',
            buttons: [{
                text: $.mage.__('Ok'),
                class: '',
                click: function () {
                    this.closeModal();
                }
            }],
            opened: function() {
                new Ajax.Request('meilisearch_merchandising/category/merch_chooser/', {
                    evalScripts: true,
                    parameters: {
                        'form_key': FORM_KEY
                    },
                    onSuccess: function(t) {
                        $('.modal-body-content').html(t.responseText);
                    }.bind(this)
                });
            }
        };

        qb.on('afterUpdateRuleFilter.queryBuilder', function(e, rule) {
            if (rule.filter.id === 'sku') {
                let input = $(rule.$el).find('.rule-value-container input');
                let button = $('<button>').text('Mon Bouton').addClass('btn btn-primary');

                input.after(button);

                button.on('click', function() {
                    $('#modal').modal('openModal');
                });
            }
        });

        modal(options, $('#modal'));
    }
});
