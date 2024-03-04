<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchMerchandising\Api\Data;

interface FacetAttributeInterface
{
    const CODE = 'code';
    const LABEL = 'label';
    const POSITION = 'position';
    const OPERATOR = 'operator';
    const LIMIT = 'limit';
    const SHOW_MORE = 'show_more';
    const SHOW_MORE_LIMIT = 'show_more_limit';
    const SEARCHABLE = 'searchable';
    const SEARCHABLE_IS_ALWAYS_ACTIVE = 'searchable_is_always_active';
    const SEARCHABLE_ESCAPE_FACET_VALUES = 'searchable_escape_facet_values';
    const FACET_ID = 'facet_id';

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $code
     * @return $this
     */
    public function setCode($code);

    /**
     * @return int
     */
    public function getPosition();

    /**
     * @param int $position
     * @return $this
     */
    public function setPosition($position);

    /**
     * @return string
     */
    public function getOperator();

    /**
     * @param string $operator
     * @return $this
     */
    public function setOperator($operator);

    /**
     * @return int
     */
    public function getLimit();

    /**
     * @param int $limit
     * @return $this
     */
    public function setLimit($limit);

    /**
     * @return bool
     */
    public function getShowMore();

    /**
     * @param bool $showMore
     * @return $this
     */
    public function setShowMore($showMore);

    /**
     * @return int
     */
    public function getShowMoreLimit();

    /**
     * @param int $showMoreLimit
     * @return $this
     */
    public function setShowMoreLimit($showMoreLimit);

    /**
     * @return bool
     */
    public function getSearchable();

    /**
     * @param bool $searchable
     * @return $this
     */
    public function setSearchable($searchable);

    /**
     * @return bool
     */
    public function getSearchableIsAlwaysActive();

    /**
     * @param bool $searchableIsAlwaysActive
     * @return $this
     */
    public function setSearchableIsAlwaysActive($searchableIsAlwaysActive);

    /**
     * @return bool
     */
    public function getSearchableEscapeFacetValues();

    /**
     * @param bool $searchableEscapeFacetValues
     * @return $this
     */
    public function setSearchableEscapeFacetValues($searchableEscapeFacetValues);

    /**
     * @return int
     */
    public function getFacetId();

    /**
     * @param int $facetId
     * @return $this
     */
    public function setFacetId($facetId);
}
