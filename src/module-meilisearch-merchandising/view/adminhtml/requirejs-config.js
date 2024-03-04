var config = {
    paths: {
        'jsTree': 'Walkwizus_MeilisearchMerchandising/js/jsTree.min',
        'jquery-extendext': 'Walkwizus_MeilisearchMerchandising/js/jquery-extendext',
        'queryBuilder': 'Walkwizus_MeilisearchMerchandising/js/query-builder.min'
    },
    shim: {
        'jsTree': {
            deps: ['jquery']
        },
        'queryBuilder': {
            deps: ['jquery', 'jquery-extendext']
        }
    }
};
