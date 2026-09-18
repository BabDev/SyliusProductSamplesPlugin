<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductVariantRepository as CoreProductVariantRepository;

/**
 * Hides sample variants from the listings Sylius builds for a product, most visibly the admin variant grid.
 */
class ProductVariantRepository extends CoreProductVariantRepository
{
    public function createQueryBuilderByProductId(string $locale, $productId): QueryBuilder
    {
        return parent::createQueryBuilderByProductId($locale, $productId)
            ->andWhere('o.sampleOf IS NULL')
        ;
    }

    public function createQueryBuilderByProductCode(string $locale, string $productCode): QueryBuilder
    {
        return parent::createQueryBuilderByProductCode($locale, $productCode)
            ->andWhere('o.sampleOf IS NULL')
        ;
    }
}
