<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchMerchandising\Api;

use Walkwizus\MeilisearchMerchandising\Api\Data\FacetInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

interface FacetRepositoryInterface
{
    /**
     * @param int $id
     * @return FacetInterface
     * @throws NoSuchEntityException
     */
    public function getById($id): FacetInterface;

    /**
     * @param string $indexName
     * @return FacetInterface
     * @throws NoSuchEntityException
     */
    public function getByIndex(string $indexName): FacetInterface;

    /**
     * @param FacetInterface $facet
     * @return FacetInterface
     * @throws CouldNotSaveException
     */
    public function save(FacetInterface $facet): FacetInterface;
}
