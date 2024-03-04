<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchMerchandising\Api;

use Walkwizus\MeilisearchMerchandising\Api\Data\FacetAttributeInterface;

interface FacetAttributeRepository
{
    /**
     * @param FacetAttributeInterface $facetAttribute
     * @return mixed
     */
    public function save(FacetAttributeInterface $facetAttribute);
}
