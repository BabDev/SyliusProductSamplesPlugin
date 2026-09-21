<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductVariantRepository as CoreProductVariantRepository;
use Sylius\Component\Product\Model\ProductVariantInterface;

/**
 * Keeps sample variants out of the places an administrator browses or searches for a variant.
 *
 * The parent repository has a dozen query methods and only some of them should hide samples. The rule is
 * that **discovery** is filtered and **resolution** is not:
 *
 * - Discovery: "show me the variants matching this" must not offer samples, because a sample is an
 *   implementation detail of the variant it belongs to and is not independently manageable. That is this
 *   class: the two admin grid query builders and the two autocomplete searches.
 * - Resolution: "give me the variant with this code or id" must still return samples, because a cart
 *   item, an API IRI and the admin autocomplete's own round-trip of already-selected values all legitimately
 *   point at one. `findByCodes()`, `findByCodesAndProductCode()`, `findOneByCodeAndProductCode()` and
 *   `findOneByIdAndProductId()` are therefore deliberately left alone, matching the same decision made in
 *   {@see \BabDev\SyliusProductSamplesPlugin\Doctrine\QueryCollectionExtension\HideSampleProductVariantsExtension}
 *   for the shop API.
 *
 * Three further methods are deliberately not overridden:
 *
 * - `getCodesOfAllVariants()` and `findByCodes()` are how `AllProductVariantsCatalogPromotionsProcessor` and
 *   `ApplyCatalogPromotionsOnVariantsHandler` feed Sylius' catalog promotions. Filtering either would decide,
 *   silently and in the wrong place, whether a catalog promotion may discount a sample. That is a pricing
 *   policy question, not a leak.
 * - `findByName()` and `findByNameAndProduct()` are used by Sylius' own Behat transformers to resolve a
 *   variant by name. Samples have names, and a scenario has to be able to look one up.
 * - `findByTaxon()` has no caller anywhere in Sylius; filtering it would add untested surface for no gain.
 */
class ProductVariantRepository extends CoreProductVariantRepository
{
    /**
     * @param mixed $productId
     */
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

    /**
     * @return array<array-key, ProductVariantInterface>
     */
    public function findByPhrase(string $phrase, string $locale, ?int $limit = null): array
    {
        /** @var array<array-key, ProductVariantInterface> $variants */
        $variants = $this->createNonSampleVariantsMatchingPhraseQueryBuilder($phrase, $locale)
            ->orderBy('o.product', 'ASC')
            ->addOrderBy('o.position', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;

        return $variants;
    }

    /**
     * @return array<array-key, ProductVariantInterface>
     */
    public function findByPhraseAndProductCode(string $phrase, string $locale, string $productCode): array
    {
        /** @var array<array-key, ProductVariantInterface> $variants */
        $variants = $this->createNonSampleVariantsMatchingPhraseQueryBuilder($phrase, $locale)
            ->innerJoin('o.product', 'product')
            ->andWhere('product.code = :productCode')
            ->setParameter('productCode', $productCode)
            ->getQuery()
            ->getResult()
        ;

        return $variants;
    }

    /**
     * Forked from the phrase matching shared by `findByPhrase()` and `findByPhraseAndProductCode()` in
     * Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductVariantRepository (Sylius 1.12).
     *
     * Those two methods return results rather than a query builder, so unlike the grid query builders above
     * there is no seam to add a condition to and the matching has to be carried here. Diff it against the
     * upstream methods when upgrading Sylius: the only intended difference is the `sampleOf IS NULL`
     * condition. The filter is applied in the query rather than to the results because `findByPhrase()`
     * applies the autocomplete's row limit, and filtering afterward would silently shrink the result set.
     */
    private function createNonSampleVariantsMatchingPhraseQueryBuilder(string $phrase, string $locale): QueryBuilder
    {
        $expr = $this->getEntityManager()->getExpressionBuilder();

        return $this->createQueryBuilder('o')
            ->innerJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
            ->andWhere($expr->orX(
                'translation.name LIKE :phrase',
                'o.code LIKE :phrase',
            ))
            ->andWhere('o.sampleOf IS NULL')
            ->setParameter('phrase', '%' . $phrase . '%')
            ->setParameter('locale', $locale)
        ;
    }
}
