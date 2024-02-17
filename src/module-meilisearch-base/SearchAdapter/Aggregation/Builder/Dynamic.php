<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\SearchAdapter\Aggregation\Builder;

use Magento\Framework\Search\Dynamic\DataProviderInterface;
use Magento\Framework\Search\Dynamic\Algorithm\Repository;
use Magento\Framework\Search\Dynamic\EntityStorageFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Search\Request\BucketInterface as RequestBucketInterface;
use Magento\Framework\Search\Request\Aggregation\DynamicBucket;
use Magento\Framework\Search\Dynamic\EntityStorage;
use Magento\Framework\Search\Dynamic\Algorithm\Improved;

class Dynamic implements BucketBuilderInterface
{
    /**
     * @var Repository
     */
    protected Repository $algorithmRepository;

    /**
     * @var EntityStorageFactory
     */
    protected EntityStorageFactory $entityStorageFactory;

    /**
     * @var CustomerSession
     */
    protected CustomerSession $customerSession;

    /**
     * @param Repository $algorithmRepository
     * @param EntityStorageFactory $entityStorageFactory
     * @param CustomerSession $customerSession
     */
    public function __construct(
        Repository $algorithmRepository,
        EntityStorageFactory $entityStorageFactory,
        CustomerSession $customerSession
    ) {
        $this->algorithmRepository = $algorithmRepository;
        $this->entityStorageFactory = $entityStorageFactory;
        $this->customerSession = $customerSession;
    }

    /**
     * @param RequestBucketInterface $bucket
     * @param array $dimensions
     * @param array $queryResult
     * @param DataProviderInterface $dataProvider
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function build(
        RequestBucketInterface $bucket,
        array $dimensions,
        array $queryResult,
        DataProviderInterface $dataProvider
    ): array {
        /** @var DynamicBucket $bucket */
        $algorithm = $this->algorithmRepository->get($bucket->getMethod(), ['dataProvider' => $dataProvider]);
        $data = $algorithm->getItems($bucket, $dimensions, $this->getEntityStorage($queryResult));
        return $this->prepareData($data);
    }

    /**
     * @param array $queryResult
     * @return EntityStorage
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getEntityStorage(array $queryResult): EntityStorage
    {
        $entityIds = [];
        foreach ($queryResult['hits'] as $product) {
            $entityIds[] = $product['id'];
        }

        $customerGroupId = $this->customerSession->getCustomerGroupId();
        $priceField = 'price_' . $customerGroupId;

        $data = [
            'query_result' => $queryResult,
            'entity_ids' => $entityIds,
            'price_field' => $priceField,
        ];

        return $this->entityStorageFactory->create($data);
    }

    /**
     * @param $data
     * @return array
     */
    private function prepareData($data): array
    {
        $resultData = [];
        foreach ($data as $value) {
            $rangeName = "{$value['from']}_{$value['to']}";
            $value['value'] = $rangeName;
            $resultData[$rangeName] = $value;
        }

        return $resultData;
    }
}
