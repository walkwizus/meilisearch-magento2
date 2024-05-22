define([
    'jquery',
    'queryBuilder',
    'Magento_Ui/js/modal/modal',
    'mage/template'
], function($, queryBuilder, modal, template) {
    return function(config, element) {
        let currentPage = 1;
        const limit = 20;
        let totalHits = 0;

        $('#category-promoted-products').sortable();

        let qb = $(config.queryBuilderContainer).queryBuilder({
            filters: config.filters
        });

        function loadMoreItems() {
            let rules = qb.queryBuilder('getRules');
            if (rules && rules.valid) {
                $.ajax(config.previewUrl, {
                    showLoader: true,
                    dataType: 'json',
                    data: {
                        form_key: window.FORM_KEY,
                        rules: rules,
                        storeId: config.storeId,
                        page: currentPage,
                        limit: limit
                    },
                    success: function(response) {
                        let promotedTemplate = template('#category-merchandising-preview-template');
                        let naturalTemplate = template('#category-merchandising-preview-template');

                        $.each(response.promoted, function(i, v) {
                            let preview = promotedTemplate({ data: { ...v } });
                            $('#category-promoted-products').append(preview);
                        });

                        $.each(response.natural, function(i, v) {
                            let preview = naturalTemplate({ data: { ...v } });
                            $('#category-merchandising-preview').append(preview);
                        });

                        currentPage++;
                        totalHits = response.natural.estimatedTotalHits;
                        if ((currentPage - 1) * limit >= totalHits) {
                            $('#load-more').hide();
                        }
                        $('#category-merchandising-preview').trigger('contentUpdated');
                    }
                });
            }
        }

        $('#load-more').on('click', function() {
            loadMoreItems();
        });

        $('#apply-rule').on('click', function() {
            $('#category-merchandising-preview').empty();
            currentPage = 1;
            $('#load-more').show();
            loadMoreItems();
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
                    },
                    success: function(r) {
                        $('#apply-rule').click();
                    }
                });
            }
        });

        $('#delete-rule').on('click', function() {
            let categoryId = $('#category_id').val();
            $.ajax(config.deleteRuleUrl, {
                showLoader: true,
                dataType: 'json',
                data: {
                    form_key: window.FORM_KEY,
                    category_id: categoryId
                },
                success: function(r) {
                    $('#query-builder').queryBuilder('setRules', [{empty: true}]);
                    $('#category-merchandising-preview').html('').trigger('contentUpdated');
                }
            })
        });

        qb.on('afterSetRules.queryBuilder', function() {
            $('#apply-rule').click();
        });

        qb.on('afterUpdateRuleFilter.queryBuilder', function(e, rule) {
            if (rule.filter.id === 'sku') {
                let rulePos = rule.getPos();
                let inputValue = $('input[name="' + rule.id + '_value_' + rulePos + '"]');
                let button = $('<button>').text('choose product(s)').addClass('btn btn-primary');

                inputValue.after(button);

                button.on('click', function() {
                    config.selectedSkuInput = inputValue;
                    $('#chooser-sku-modal').modal('openModal');
                });
            }
        });

        $('body').on('click', '.promote-product', function(e) {
            e.preventDefault();

            let categoryId = $('#category_id').val();
            let productId = $(this).data('product-id');

            $.ajax(config.promoteProductUrl, {
                showLoader: true,
                dataType: 'json',
                data: {
                    form_key: window.FORM_KEY,
                    product_id: productId,
                    store_id: config.storeId,
                    category_id: categoryId
                },
                success: function(r) {
                    console.log(r);
                }
            });
        });

        let options = {
            type: 'slide',
            responsive: true,
            title: 'Choose Product(s)',
            buttons: [{
                text: $.mage.__('Validate'),
                class: 'action',
                click: function () {
                    this.closeModal();
                }
            }],
            opened: function() {
                $.ajax(config.productChooserUrl, {
                    showLoader: true,
                    data: {
                        form_key: window.FORM_KEY
                    },
                    success: function(r) {
                        $('.modal-body-content').html(r);
                    }
                });
            }
        };

        modal(options, $('#chooser-sku-modal'));
    }
});
