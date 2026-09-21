<?php

declare(strict_types=1);

namespace spec\BabDev\SyliusProductSamplesPlugin\Doctrine\QueryCollectionExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\ContextAwareQueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariant;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\ProductVariant as CoreProductVariant;
use Sylius\Component\User\Model\UserInterface;

final class HideSampleProductVariantsExtensionSpec extends ObjectBehavior
{
    public function let(UserContextInterface $userContext): void
    {
        $this->beConstructedWith($userContext);
    }

    public function it_is_a_query_collection_extension(): void
    {
        $this->shouldImplement(ContextAwareQueryCollectionExtensionInterface::class);
    }

    public function it_hides_samples_from_a_shop_consumer(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $userContext->getUser()->willReturn(null);
        $queryBuilder->getRootAliases()->willReturn(['o']);

        $this->applyToCollection($queryBuilder, $queryNameGenerator, ProductVariant::class, 'shop_get');

        $queryBuilder->andWhere('o.sampleOf IS NULL')->shouldHaveBeenCalled();
    }

    public function it_leaves_the_query_alone_for_an_admin_consumer(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        UserInterface $user,
    ): void {
        $user->getRoles()->willReturn(['ROLE_API_ACCESS']);
        $userContext->getUser()->willReturn($user);

        $this->applyToCollection($queryBuilder, $queryNameGenerator, ProductVariant::class, 'admin_get');

        $queryBuilder->andWhere(Argument::any())->shouldNotHaveBeenCalled();
    }

    public function it_leaves_the_query_alone_for_a_variant_model_without_samples(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $userContext->getUser()->willReturn(null);

        /*
         * A store which has not replaced its product variant model has no sampleOf column, so the condition
         * must not be added — it would be invalid DQL.
         */
        $this->applyToCollection($queryBuilder, $queryNameGenerator, CoreProductVariant::class, 'shop_get');

        $queryBuilder->andWhere(Argument::any())->shouldNotHaveBeenCalled();
    }
}
