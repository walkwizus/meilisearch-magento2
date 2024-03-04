<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchMerchandising\Model;

use Magento\Framework\Model\AbstractModel;
use Walkwizus\MeilisearchMerchandising\Api\Data\FacetAttributeInterface;

class FacetAttribute extends AbstractModel implements FacetAttributeInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\FacetAttribute::class);
    }

    /**
     * @ingeritdoc
     */
    public function getLabel()
    {
        return $this->getData(self::LABEL);
    }

    /**
     * @ingeritdoc
     */
    public function setLabel($label)
    {
        return $this->setData(self::LABEL, $label);
    }

    /**
     * @ingeritdoc
     */
    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    /**
     * @ingeritdoc
     */
    public function setCode($code)
    {
        return $this->setData(self::CODE, $code);
    }

    /**
     * @ingeritdoc
     */
    public function getPosition()
    {
        return $this->getData(self::POSITION);
    }

    /**
     * @ingeritdoc
     */
    public function setPosition($position)
    {
        return $this->setData(self::POSITION, $position);
    }

    /**
     * @ingeritdoc
     */
    public function getOperator()
    {
        return $this->getData(self::OPERATOR);
    }

    /**
     * @ingeritdoc
     */
    public function setOperator($operator)
    {
        return $this->setData(self::OPERATOR, $operator);
    }

    /**
     * @ingeritdoc
     */
    public function getLimit()
    {
        return $this->getData(self::LIMIT);
    }

    /**
     * @ingeritdoc
     */
    public function setLimit($limit)
    {
        return $this->setData(self::LIMIT, $limit);
    }

    /**
     * @ingeritdoc
     */
    public function getShowMore()
    {
        return $this->getData(self::SHOW_MORE);
    }

    /**
     * @ingeritdoc
     */
    public function setShowMore($showMore)
    {
        return $this->setData(self::SHOW_MORE, $showMore);
    }

    /**
     * @ingeritdoc
     */
    public function getShowMoreLimit()
    {
        return $this->getData(self::SHOW_MORE_LIMIT);
    }

    /**
     * @ingeritdoc
     */
    public function setShowMoreLimit($showMoreLimit)
    {
        return $this->setData(self::SHOW_MORE_LIMIT, $showMoreLimit);
    }

    /**
     * @ingeritdoc
     */
    public function getSearchable()
    {
        return $this->getData(self::SEARCHABLE);
    }

    /**
     * @ingeritdoc
     */
    public function setSearchable($searchable)
    {
        return $this->setData(self::SEARCHABLE, $searchable);
    }

    /**
     * @ingeritdoc
     */
    public function getSearchableIsAlwaysActive()
    {
        return $this->getData(self::SEARCHABLE_IS_ALWAYS_ACTIVE);
    }

    /**
     * @ingeritdoc
     */
    public function setSearchableIsAlwaysActive($searchableIsAlwaysActive)
    {
        return $this->setData(self::SEARCHABLE_IS_ALWAYS_ACTIVE, $searchableIsAlwaysActive);
    }

    /**
     * @ingeritdoc
     */
    public function getSearchableEscapeFacetValues()
    {
        return $this->getData(self::SEARCHABLE_ESCAPE_FACET_VALUES);
    }

    /**
     * @ingeritdoc
     */
    public function setSearchableEscapeFacetValues($searchableEscapeFacetValues)
    {
        return $this->setData(self::SEARCHABLE_ESCAPE_FACET_VALUES, $searchableEscapeFacetValues);
    }

    /**
     * @ingeritdoc
     */
    public function getFacetId()
    {
        return $this->getData(self::FACET_ID);
    }

    /**
     * @ingeritdoc
     */
    public function setFacetId($facetId)
    {
        return $this->setData(self::FACET_ID, $facetId);
    }
}
