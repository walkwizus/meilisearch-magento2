define([
    'jquery',
    'uiComponent',
    'ko',
    'queryBuilder',
    'Walkwizus_MeilisearchMerchandising/js/category/model/merchandising',
    'jquery/ui'
], function ($, Component, ko, queryBuilder, merchandising, ui) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Walkwizus_MeilisearchMerchandising/category/merchandising',
            qb: null,
            merchandising: merchandising
        },
        initialize: function() {
            this._super();
            this.filters = JSON.parse(this.filters);
            this.initQueryBuilder();

            merchandising.currentCategoryId.subscribe(this.loadRules.bind(this));
            merchandising.preview.subscribe(this.sortPreview.bind(this));
        },
        initQueryBuilder: function() {
            let self = this;

            ko.bindingHandlers.initQueryBuilder = {
                init: function (element) {
                    self.qb = $(element).queryBuilder({
                        filters: self.filters
                    });
                }
            };
        },
        loadRules: function(categoryId) {
            let self = this;

            $.ajax(this.ajaxUrl.loadRule, {
                type: 'POST',
                data: {
                    form_key: window.FORM_KEY,
                    category_id: categoryId
                },
                dataType: 'json',
                showLoader: true,
                success: function(r) {
                    if (r.id > 0) {
                        merchandising.currentRule(r);
                        self.qb.queryBuilder('setRules', JSON.parse(r.query));
                    } else {
                        merchandising.currentRule(null);
                        self.qb.queryBuilder('setRules', [{empty: true}]);
                    }
                }
            });
        },
        saveRule: function() {
            let self = this;
            let rules = self.qb.queryBuilder('getRules');

            if (rules) {
                $.ajax(this.ajaxUrl.saveRule, {
                    type: 'POST',
                    data: {
                        form_key: window.FORM_KEY,
                        storeId: self.storeId,
                        categoryId: merchandising.currentCategoryId(),
                        rules: JSON.stringify(rules)
                    },
                    dataType: 'json',
                    showLoader: true,
                    success: function(r) {
                        merchandising.message(r.message);
                    }
                });
            }
        },
        deleteRule: function() {
            $.ajax(this.ajaxUrl.deleteRule, {
                type: 'POST',
                data: {
                    form_key: window.FORM_KEY,
                    categoryId: merchandising.currentCategoryId()
                },
                dataType: 'json',
                showLoader: true,
                success: function(r) {
                    merchandising.message(r.message);
                }
            });
        },
        preview: function() {
            let self = this;
            let rules = self.qb.queryBuilder('getRules');

            if (rules) {
                $.ajax(this.ajaxUrl.preview, {
                    type: 'POST',
                    data: {
                        form_key: window.FORM_KEY,
                        rules: JSON.stringify(rules),
                        storeId: self.storeId,
                        categoryId: merchandising.currentCategoryId()
                    },
                    dataType: 'json',
                    showLoader: true,
                    success: function(r) {
                        merchandising.preview(r);
                    }
                });
            }
        },
        sortPreview: function() {
            $('#category-merchandising-preview .product-grid').sortable({
                placeholder: 'sortable-placeholder',
                update: function(event, ui) {
                    let sortedIds = $(this).children('.product-card').map(function() {
                        return $(this).data('product-id');
                    }).get();

                    let sortedArray = [];
                    sortedIds.forEach(function(id) {
                        let item = ko.utils.arrayFirst(merchandising.preview(), function(product) {
                            return product.id === id;
                        });
                        if (item) {
                            sortedArray.push(item);
                        }
                    });

                    merchandising.preview(sortedArray);
                }
            });
        }
    });
});
