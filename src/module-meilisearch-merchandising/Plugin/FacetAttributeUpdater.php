<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchMerchandising\Plugin;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Walkwizus\MeilisearchBase\Model\Adapter\Meilisearch;
use Walkwizus\MeilisearchCatalog\Service\GetRefinementListService;
use Walkwizus\MeilisearchMerchandising\Model\FacetFactory;
use Walkwizus\MeilisearchMerchandising\Model\FacetAttributeFactory;
use Walkwizus\MeilisearchMerchandising\Api\FacetRepositoryInterface;
use Walkwizus\MeilisearchMerchandising\Api\FacetAttributeRepositoryInterface;
use Walkwizus\MeilisearchMerchandising\Model\Config\Source\FacetAttribute\Operator;

class FacetAttributeUpdater
{
    /**
     * @param GetRefinementListService $getRefinementListService
     * @param FacetFactory $facetFactory
     * @param FacetAttributeFactory $facetAttributeFactory
     * @param FacetRepositoryInterface $facetRepository
     * @param FacetAttributeRepositoryInterface $facetAttributeRepository
     */
    public function __construct(
        private GetRefinementListService $getRefinementListService,
        private FacetFactory $facetFactory,
        private FacetAttributeFactory $facetAttributeFactory,
        private FacetRepositoryInterface $facetRepository,
        private FacetAttributeRepositoryInterface $facetAttributeRepository
    ) { }

    /**
     * @param Meilisearch $subject
     * @param array $settings
     * @param $storeId
     * @param $mappedIndexerId
     * @return void
     * @throws LocalizedException
     */
    public function beforeUpdateSettings(Meilisearch $subject, array $settings, $storeId, $mappedIndexerId): void
    {
        $refinementList = $this->getRefinementListService->get($mappedIndexerId);

        if (count($refinementList) > 0) {
            try {
                $facet = $this->facetRepository->getByIndex($mappedIndexerId);
                $facet->setIndexName($mappedIndexerId);
                $this->facetRepository->save($facet);

                $position = 0;
                foreach ($refinementList as $code => $label) {
                    try {
                        $facetAttribute = $this->facetAttributeRepository->getByCode($code);
                        $facetAttribute->setLabel($label);
                        $this->facetAttributeRepository->save($facetAttribute);
                    } catch (NoSuchEntityException $noSuchEntityException) {
                        $facetAttribute = $this->facetAttributeFactory->create();

                        $facetAttribute->setCode($code);
                        $facetAttribute->setLabel($label);
                        $facetAttribute->setPosition($position++);
                        $facetAttribute->setOperator(Operator::OPERATOR_OR);
                        $facetAttribute->setLimit(0);
                        $facetAttribute->setShowMore(false);
                        $facetAttribute->setShowMoreLimit(false);
                        $facetAttribute->setSearchable(false);
                        $facetAttribute->setSearchableIsAlwaysActive(true);
                        $facetAttribute->setSearchableEscapeFacetValues(true);
                        $facetAttribute->setFacetId($facet->getId());
                        $this->facetAttributeRepository->save($facetAttribute);
                    }
                }
            } catch (NoSuchEntityException $noSuchEntityException) {
                $facet = $this->facetFactory->create();
                $facet->setIndexName($mappedIndexerId);
                $this->facetRepository->save($facet);

                $position = 0;
                foreach ($refinementList as $code => $label) {
                    try {
                        $facetAttribute = $this->facetAttributeRepository->getByCode($code);
                        $facetAttribute->setLabel($label);
                        $this->facetAttributeRepository->save($facetAttribute);
                    } catch (NoSuchEntityException $noSuchEntityException) {
                        $facetAttribute = $this->facetAttributeFactory->create();

                        $facetAttribute->setCode($code);
                        $facetAttribute->setLabel($label);
                        $facetAttribute->setPosition($position++);
                        $facetAttribute->setOperator(Operator::OPERATOR_OR);
                        $facetAttribute->setLimit(0);
                        $facetAttribute->setShowMore(false);
                        $facetAttribute->setShowMoreLimit(false);
                        $facetAttribute->setSearchable(false);
                        $facetAttribute->setSearchableIsAlwaysActive(true);
                        $facetAttribute->setSearchableEscapeFacetValues(true);
                        $facetAttribute->setFacetId($facet->getId());
                        $this->facetAttributeRepository->save($facetAttribute);
                    }
                }
            } catch (CouldNotSaveException $e) {

            }
        }
    }
}
