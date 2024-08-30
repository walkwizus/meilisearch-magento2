# <p align="center">Meilisearch / Magento 2 (Adobe Commerce)</p>

The Meilisearch extension for Magento 2 enables replacing Magento's default search engine (OpenSearch) with Meilisearch.

## Prerequisites

* Magento >= 2.4.4
* Meilisearch >= v1.9.0
* PHP >= 8.1

Magento 2 module install

```
composer require walkwizus/meilisearch
bin/magento module:enable Walkwizus_MeilisearchBase Walkwizus_MeilisearchCatalog Walkwizus_MeilisearchInstantSearch Walkwizus_MeilisearchMerchandising
bin/magento setup:upgrade
```

## Configuration

```
bin/magento config:set meilisearch_server/settings/address meilisearch:7700
bin/magento config:set meilisearch_server/settings/api_key "YOUR_API_KEY"
bin/magento config:set meilisearch_server/settings/client_adress localhost:7700
bin/magento config:set meilisearch_server/settings/client_api_key "YOUR_CLIENT_API_KEY"
```

## Indexing

```
bin/magento indexer:reindex catalogsearch_fulltext
bin/magento indexer:reindex meilisearch_categories_fulltext
```
