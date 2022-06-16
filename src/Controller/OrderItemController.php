<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Controller;

use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use FOS\RestBundle\View\View;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Bundle\OrderBundle\Controller\OrderItemController as BaseOrderItemController;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\CartActions;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Webmozart\Assert\Assert;

class OrderItemController extends BaseOrderItemController
{
    /**
     * Extends the "add to cart" controller action to handle changing the requested product variant to its corresponding
     * sample variant when the "request a sample" button is clicked.
     */
    public function addAction(Request $request): Response
    {
        // GET requests will be handled by the parent controller
        if (!$request->isMethod('POST')) {
            return parent::addAction($request);
        }

        $cart = $this->getCurrentCart();
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, CartActions::ADD);

        /** @var OrderItemInterface $orderItem */
        $orderItem = $this->newResourceFactory->create($configuration, $this->factory);

        $this->getQuantityModifier()->modify($orderItem, 1);

        $form = $this->getFormFactory()->create(
            $configuration->getFormType(),
            $this->createAddToCartCommand($cart, $orderItem),
            $configuration->getFormOptions()
        );

        // We are only interested in submissions where the "request a sample" button was clicked
        if (!$form->has('requestSample')) {
            return parent::addAction($request);
        }

        $form->handleRequest($request);

        /** @var SubmitButton $button */
        $button = $form->get('requestSample');

        if (!$button->isClicked()) {
            return parent::addAction($request);
        }

        if (!$form->isValid()) {
            return $this->handleBadAjaxRequestView($configuration, $form);
        }

        /** @var AddToCartCommandInterface $addToCartCommand */
        $addToCartCommand = $form->getData();

        /** @var OrderItemInterface $cartItem */
        $cartItem = $addToCartCommand->getCartItem();

        Assert::isInstanceOf($cartItem->getVariant(), ProductVariantInterface::class);

        $cartItem->setVariant($cartItem->getVariant()->getSample());

        $errors = $this->getCartItemErrors($addToCartCommand->getCartItem());

        if (0 < count($errors)) {
            $form = $this->getAddToCartFormWithErrors($errors, $form);

            return $this->handleBadAjaxRequestView($configuration, $form);
        }

        $event = $this->eventDispatcher->dispatchPreEvent(CartActions::ADD, $configuration, $orderItem);

        if ($event->isStopped()) {
            if (!$configuration->isHtmlRequest()) {
                throw new HttpException($event->getErrorCode(), $event->getMessage());
            }

            $this->flashHelper->addFlashFromEvent($configuration, $event);

            return $this->redirectHandler->redirectToIndex($configuration, $orderItem);
        }

        $this->getOrderModifier()->addToOrder($addToCartCommand->getCart(), $addToCartCommand->getCartItem());

        $cartManager = $this->getCartManager();
        $cartManager->persist($cart);
        $cartManager->flush();

        $resourceControllerEvent = $this->eventDispatcher->dispatchPostEvent(CartActions::ADD, $configuration, $orderItem);

        if ($resourceControllerEvent->hasResponse()) {
            return $resourceControllerEvent->getResponse();
        }

        $this->flashHelper->addSuccessFlash($configuration, CartActions::ADD, $orderItem);

        if ($request->isXmlHttpRequest()) {
            return $this->viewHandler->handle($configuration, View::create([], Response::HTTP_CREATED));
        }

        return $this->redirectHandler->redirectToResource($configuration, $orderItem);
    }
}
