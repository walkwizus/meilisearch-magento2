<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchMerchandising\Model;

use Walkwizus\MeilisearchMerchandising\Api\FacetRepositoryInterface;
use Walkwizus\MeilisearchMerchandising\Model\ResourceModel\Facet;
use Walkwizus\MeilisearchMerchandising\Api\Data\FacetInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class FacetRepository implements FacetRepositoryInterface
{
    /**
     * @var Facet
     */
    private Facet $facetResource;

    /**
     * @var FacetFactory
     */
    private FacetFactory $facetFactory;

    /**
     * @param Facet $facetResource
     * @param FacetFactory $facetFactory
     */
    public function __construct(
        Facet $facetResource,
        FacetFactory $facetFactory
    ) {
        $this->facetResource = $facetResource;
        $this->facetFactory = $facetFactory;
    }

    /**
     * @param int $id
     * @return FacetInterface
     * @throws NoSuchEntityException
     */
    public function getById($id): FacetInterface
    {
        $facet = $this->facetFactory->create();
        $this->facetResource->load($facet, $id);

        if (!$facet->getId()) {
            throw new NoSuchEntityException(__('The facet with the "%1" ID doesn\'t exist.', $id));
        }

        return $facet;
    }

    /**
     * @param string $indexName
     * @return FacetInterface
     * @throws NoSuchEntityException
     */
    public function getByIndex(string $indexName): FacetInterface
    {
        $facet = $this->facetFactory->create();
        $this->facetResource->load($facet, $indexName, FacetInterface::INDEX_NAME);

        if (!$facet->getId()) {
            throw new NoSuchEntityException(__('The facet with the "%1" INDEX NAME doesn\'t exist.', $indexName));
        }

        return $facet;
    }

    /**
     * @param FacetInterface $facet
     * @return FacetInterface
     * @throws CouldNotSaveException
     */
    public function save(FacetInterface $facet): FacetInterface
    {
        try {
            $this->facetResource->save($facet);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }

        return $facet;
    }
}
