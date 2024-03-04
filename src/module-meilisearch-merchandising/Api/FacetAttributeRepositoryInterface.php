<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchMerchandising\Api;

use Walkwizus\MeilisearchMerchandising\Api\Data\FacetAttributeInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;

interface FacetAttributeRepositoryInterface
{
    /**
     * @param int $id
     * @return FacetAttributeInterface
     * @throws NoSuchEntityException
     */
    public function getBydId($id): FacetAttributeInterface;

    /**
     * @param int $facetId
     * @return FacetAttributeInterface
     * @throws NoSuchEntityException
     */
    public function getByFacetId($facetId): FacetAttributeInterface;

    /**
     * @param string $code
     * @return FacetAttributeInterface
     * @throws NoSuchEntityException
     */
    public function getByCode(string $code): FacetAttributeInterface;

    /**
     * @param FacetAttributeInterface $facetAttribute
     * @return FacetAttributeInterface
     * @throws CouldNotSaveException
     */
    public function save(FacetAttributeInterface $facetAttribute): FacetAttributeInterface;
}
