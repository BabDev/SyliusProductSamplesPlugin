<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Behat\Page\Shop\Product;

use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Session;
use DMore\ChromeDriver\ChromeDriver;
use Sylius\Behat\Page\Shop\Cart\SummaryPageInterface;
use Sylius\Behat\Page\Shop\Product\ShowPage as BaseShowPage;
use Sylius\Behat\Service\JQueryHelper;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Symfony\Component\Routing\RouterInterface;

class ShowPage extends BaseShowPage implements ShowPageInterface
{
    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        private SummaryPageInterface $summaryPage
    ) {
        parent::__construct($session, $minkParameters, $router, $summaryPage);
    }

    /**
     * @throws ElementNotFoundException
     */
    public function requestASampleWithOption(ProductOptionInterface $option, string $optionValue): void
    {
        $select = $this->getElement('option_select', ['%optionCode%' => $option->getCode()]);

        $this->getDocument()->selectFieldOption($select->getAttribute('name'), $optionValue);

        $requestASampleButton = $this->getElement('request_a_sample_button');
        $requestASampleButton->focus();
        $requestASampleButton->press();

        $this->waitForCartSummary();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'request_a_sample_button' => '[data-test-request-a-sample-button]',
        ]);
    }

    private function waitForCartSummary(): void
    {
        if ($this->getDriver() instanceof Selenium2Driver || $this->getDriver() instanceof ChromeDriver) {
            JQueryHelper::waitForAsynchronousActionsToFinish($this->getSession());
            $this->getDocument()->waitFor(3, fn (): bool => $this->summaryPage->isOpen());
        }
    }
}
