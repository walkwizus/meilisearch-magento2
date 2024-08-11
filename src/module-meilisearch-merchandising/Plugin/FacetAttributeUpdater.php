<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchMerchandising\Plugin;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Walkwizus\MeilisearchCatalog\Service\GetRefinementListService;
use Walkwizus\MeilisearchMerchandising\Model\FacetFactory;
use Walkwizus\MeilisearchMerchandising\Model\FacetAttributeFactory;
use Walkwizus\MeilisearchMerchandising\Api\FacetRepositoryInterface;
use Walkwizus\MeilisearchMerchandising\Api\FacetAttributeRepositoryInterface;
use Walkwizus\MeilisearchMerchandising\Model\Config\Source\FacetAttribute\Operator;
use Walkwizus\MeilisearchBase\Model\Indexer\BaseIndexerHandler;

class FacetAttributeUpdater
{
    /**
     * @var GetRefinementListService
     */
    private GetRefinementListService $getRefinementListService;

    /**
     * @var FacetFactory
     */
    private FacetFactory $facetFactory;

    /**
     * @var FacetAttributeFactory
     */
    private FacetAttributeFactory $facetAttributeFactory;

    /**
     * @var FacetRepositoryInterface
     */
    private FacetRepositoryInterface $facetRepository;

    /**
     * @var FacetAttributeRepositoryInterface
     */
    private FacetAttributeRepositoryInterface $facetAttributeRepository;

    /**
     * @param GetRefinementListService $getRefinementListService
     * @param FacetFactory $facetFactory
     * @param FacetAttributeFactory $facetAttributeFactory
     * @param FacetRepositoryInterface $facetRepository
     * @param FacetAttributeRepositoryInterface $facetAttributeRepository
     */
    public function __construct(
        GetRefinementListService $getRefinementListService,
        FacetFactory $facetFactory,
        FacetAttributeFactory $facetAttributeFactory,
        FacetRepositoryInterface $facetRepository,
        FacetAttributeRepositoryInterface $facetAttributeRepository
    ) {
        $this->getRefinementListService = $getRefinementListService;
        $this->facetFactory = $facetFactory;
        $this->facetAttributeFactory = $facetAttributeFactory;
        $this->facetRepository = $facetRepository;
        $this->facetAttributeRepository = $facetAttributeRepository;
    }

    /**
     * @param BaseIndexerHandler $subject
     * @param $dimensions
     * @param \Traversable $documents
     * @return null
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function beforeSaveIndex(BaseIndexerHandler $subject, $dimensions, \Traversable $documents)
    {
        $indexName = $subject->getIndexerId();

        if ($indexName != 'catalog_product') {
            return null;
        }

        $refinementList = $this->getRefinementListService->get($indexName);

        if (count($refinementList) > 0) {
            try {
                $facet = $this->facetRepository->getByIndex($indexName);
                $facet->setIndexName($indexName);
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
                $facet->setIndexName($indexName);
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

        return null;
    }
}
