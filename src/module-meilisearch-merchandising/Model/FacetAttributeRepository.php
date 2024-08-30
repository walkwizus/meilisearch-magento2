<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchMerchandising\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Walkwizus\MeilisearchMerchandising\Api\FacetAttributeRepositoryInterface;
use Walkwizus\MeilisearchMerchandising\Api\Data\FacetAttributeInterface;
use Walkwizus\MeilisearchMerchandising\Model\ResourceModel\FacetAttribute as FacetAttributeResource;
use Magento\Framework\Exception\CouldNotSaveException;

class FacetAttributeRepository implements FacetAttributeRepositoryInterface
{
    /**
     * @param FacetAttributeResource $facetAttributeResource
     * @param FacetAttributeFactory $facetAttributeFactory
     */
    public function __construct(
        private FacetAttributeResource $facetAttributeResource,
        private FacetAttributeFactory $facetAttributeFactory
    ) { }

    /**
     * @param $id
     * @return FacetAttributeInterface
     * @throws NoSuchEntityException
     */
    public function getBydId($id): FacetAttributeInterface
    {
        $facetAttribute = $this->facetAttributeFactory->create();
        $this->facetAttributeResource->load($facetAttribute, $id);

        if (!$facetAttribute->getId()) {
            throw new NoSuchEntityException(__('The facet attribute with the "%1" ID doesn\'t exist.', $id));
        }

        return $facetAttribute;
    }

    /**
     * @param int $facetId
     * @return FacetAttributeInterface
     * @throws NoSuchEntityException
     */
    public function getByFacetId($facetId): FacetAttributeInterface
    {
        $facetAttribute = $this->facetAttributeFactory->create();
        $this->facetAttributeResource->load($facetAttribute, $facetId, FacetAttributeInterface::FACET_ID);

        if (!$facetAttribute->getId()) {
            throw new NoSuchEntityException(__('The facet attribute with the "%1" FACET ID doesn\'t exist.', $facetId));
        }

        return $facetAttribute;
    }

    /**
     * @param string $code
     * @return FacetAttributeInterface
     * @throws NoSuchEntityException
     */
    public function getByCode(string $code): FacetAttributeInterface
    {
        $facetAttribute = $this->facetAttributeFactory->create();
        $this->facetAttributeResource->load($facetAttribute, $code, FacetAttributeInterface::CODE);

        if (!$facetAttribute->getId()) {
            throw new NoSuchEntityException(__('The facet attribute with the "%1" CODE doesn\'t exist.', $code));
        }

        return $facetAttribute;
    }

    /**
     * @param FacetAttributeInterface $facetAttribute
     * @return FacetAttributeInterface
     * @throws CouldNotSaveException
     */
    public function save(FacetAttributeInterface $facetAttribute): FacetAttributeInterface
    {
        try {
            $this->facetAttributeResource->save($facetAttribute);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }

        return $facetAttribute;
    }
}
