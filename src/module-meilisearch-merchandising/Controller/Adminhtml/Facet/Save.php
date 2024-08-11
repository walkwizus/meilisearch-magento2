<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchMerchandising\Controller\Adminhtml\Facet;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Walkwizus\MeilisearchMerchandising\Api\FacetAttributeRepositoryInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\Model\View\Result\Redirect;

class Save extends Action implements HttpPostActionInterface
{
    /**
     * @var FacetAttributeRepositoryInterface
     */
    private FacetAttributeRepositoryInterface $facetAttributeRepository;

    /**
     * @param Context $context
     * @param FacetAttributeRepositoryInterface $facetAttributeRepository
     */
    public function __construct(
        Context $context,
        FacetAttributeRepositoryInterface $facetAttributeRepository
    ) {
        $this->facetAttributeRepository = $facetAttributeRepository;
        parent::__construct($context);
    }

    /**
     * @return Redirect
     */
    public function execute(): Redirect
    {
        $facetId = $this->getRequest()->getParam('id');
        $facetAttributes = $this->getRequest()->getParam('facet_attributes');
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        foreach ($facetAttributes as $facetAttribute) {
            if ($facetAttribute['facet_id']) {
                try {
                    $facetAttributeModel = $this->facetAttributeRepository->getBydId($facetAttribute['id']);
                } catch (NoSuchEntityException $noSuchEntityException) {
                    $this->messageManager->addErrorMessage($noSuchEntityException->getMessage());
                    return $redirect->setPath('*/*/');
                }

                $facetAttributeModel->setCode($facetAttribute['code']);
                $facetAttributeModel->setPosition((int)$facetAttribute['position']);
                $facetAttributeModel->setOperator($facetAttribute['operator']);
                $facetAttributeModel->setLimit((int)$facetAttribute['limit']);
                $facetAttributeModel->setShowMore((bool)$facetAttribute['show_more']);
                $facetAttributeModel->setShowMoreLimit((int)$facetAttribute['show_more_limit']);
                $facetAttributeModel->setSearchable((bool)$facetAttribute['searchable']);
                $facetAttributeModel->setFacetId((int)$facetAttribute['facet_id']);

                try {
                    $this->facetAttributeRepository->save($facetAttributeModel);
                } catch (CouldNotSaveException $couldNotSaveException) {
                    $this->messageManager->addErrorMessage($couldNotSaveException->getMessage());
                    return $redirect->setPath('*/*/');
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                    return $redirect->setPath('*/*/');
                }
            }
        }

        if ($this->getRequest()->getParam('back')) {
            return $redirect->setPath('*/*/edit', ['id' => $facetId]);
        }

        return $redirect->setPath('*/*/index');
    }
}
