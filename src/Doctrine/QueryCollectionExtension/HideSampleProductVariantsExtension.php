<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Doctrine\QueryCollectionExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\ContextAwareQueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Keeps sample variants out of the shop API's product variant collection.
 *
 * This is the API's counterpart to {@see \BabDev\SyliusProductSamplesPlugin\Doctrine\ORM\ProductVariantRepository},
 * which hides samples from the admin grid, and to `Product::getEnabledVariants()`, which hides them from
 * the storefront. Samples are still reachable by their own IRI, because a cart item legitimately points at
 * one and a client has to be able to resolve it.
 *
 * Admin API consumers are left alone, matching how Sylius' own shop-facing extensions behave.
 */
final class HideSampleProductVariantsExtension implements ContextAwareQueryCollectionExtensionInterface
{
    public function __construct(private readonly UserContextInterface $userContext)
    {
    }

    /**
     * @param array<array-key, mixed> $context
     */
    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?string $operationName = null,
        array $context = [],
    ): void {
        if (!is_a($resourceClass, ProductVariantInterface::class, true)) {
            return;
        }

        $user = $this->userContext->getUser();

        if ($user instanceof UserInterface && \in_array('ROLE_API_ACCESS', $user->getRoles(), true)) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        $queryBuilder->andWhere(sprintf('%s.sampleOf IS NULL', $rootAlias));
    }
}
