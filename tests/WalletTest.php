<?php
namespace Andrey\PHPTest;

use PHPUnit\Framework\TestCase;

require "./src/Wallet.php";

final class WalletTest extends TestCase
{
    private $rates;
    private $wall;

    protected function setUp(): void
    {
        $rates = [
            "CZK" => 1.0,
            "USD" => 22.0,
            "EUR" => 26.0
        ];
        $this->wall = new Wallet("Test", $rates);
        parent::setUp();
    }

    protected function tearDown(): void
    {
        unset($this->wall);
        parent::tearDown();
    }

    public function testClassConstructor()
    {
        $this->assertSame("Test", $this->wall->owner);
    }

    public function testgetCurrRate()
    {
        $this->assertSame(1.0, $this->wall->getCurrRate("CZK"));
        $this->assertSame(22.0, $this->wall->getCurrRate("USD"));
        $this->assertSame(26.0, $this->wall->getCurrRate("EUR"));
    }

    public function testsetCurrValue()
    {
        $this->assertSame(200.0, $this->wall->setCurrValue("USD", 200.0));
        $this->assertIsFloat($this->wall->setCurrValue("USD", 200.0));
        $this->assertSame(-1.0, $this->wall->setCurrValue("CZK", -1200.0));
    }

    public function testgetCurrValue()
    {
        $this->assertSame(200.0, $this->wall->setCurrValue("USD", 200.0));
        $this->assertSame(200.0, $this->wall->getCurrValue("USD"));
        $this->assertIsFloat($this->wall->getCurrValue("USD"));

        $this->assertSame(1200.0, $this->wall->setCurrValue("RUB", 1200.0));
        $this->assertSame(1200.0, $this->wall->getCurrValue("RUB"));

    }

    public function testgetCurrencies()
    {
        $this->wall->setCurrValue("USD", 200.0);
        $this->wall->setCurrValue("EUR", 200.0);
        $this->wall->setCurrValue("RUB", 200.0);
        $this->assertIsArray($this->wall->getCurrencies());
        $this->assertSame(["CZK", "USD", "EUR", "RUB"], $this->wall->getCurrencies());

    }

    public function testchangeCurrByValue()
    {
        $this->assertSame(1000.0, $this->wall->changeCurrByValue("USD", 1000.0));
        $this->assertSame(1000.0, $this->wall->getCurrValue("USD"));
        $this->assertSame(1500.0, $this->wall->changeCurrByValue("USD", 500.0));
        $this->assertSame(1500.0, $this->wall->getCurrValue("USD"));
        $this->assertSame(500.0, $this->wall->changeCurrByValue("USD", -1000.0));
        $this->assertSame(500.0, $this->wall->getCurrValue("USD"));
        $this->assertSame(-1.0, $this->wall->changeCurrByValue("USD", -1000.0));
        $this->assertSame(500.0, $this->wall->getCurrValue("USD"));

    }

    public function testExchangeRate()
    {
        $this->assertSame(1.1818, round($this->wall->getExchangeRate("EUR", "USD"), 4));
        $this->assertSame(26.0, round($this->wall->getExchangeRate("EUR", "CZK"), 4));
        $this->assertSame(22.0, round($this->wall->getExchangeRate("USD", "CZK"), 4));
        $this->assertSame(-1.0, round($this->wall->getExchangeRate("EUR", "RUB"), 4));

    }

    public function testcurrencyExchange()
    {
        $this->wall->setCurrValue("EUR", 1200.0);
        $this->assertSame(590.91, round($this->wall->currencyExchange("EUR", "USD", 500.0), 2));
        $this->assertSame(590.91, round($this->wall->getCurrValue("USD"), 2));
        $this->assertSame(700.0, round($this->wall->getCurrValue("EUR"), 2));

        $this->assertSame(-1.0, round($this->wall->currencyExchange("USD", "CZK", 700.0), 2));
        $this->assertSame(590.91, round($this->wall->getCurrValue("USD"), 2));
        $this->assertSame(0.0, round($this->wall->getCurrValue("CZK"), 2));

        $this->assertSame(-1.0, round($this->wall->currencyExchange("USD", "RUB", 100), 2));
        $this->assertSame(590.91, round($this->wall->getCurrValue("USD"), 2));

        $this->assertSame(-1.0, round($this->wall->currencyExchange("USD", "EUR", -1000.0), 2));
        $this->assertSame(590.91, round($this->wall->getCurrValue("USD"), 2));

    }

    public function testcalcExchange()
    {
        $this->wall->setCurrValue("EUR", 1200.0);
        $this->assertSame(590.91, round($this->wall->currencyExchange("EUR", "USD", 500.0), 2));

        $this->assertSame(-1.0, round($this->wall->currencyExchange("USD", "RUB", 100), 2));

        $this->assertSame(-1.0, round($this->wall->currencyExchange("USD", "EUR", -1000.0), 2));

    }

    public function testgetBallance()
    {
        $this->wall->setCurrValue("EUR", 100.0);
        $this->wall->setCurrValue("USD", 100.0);
        $this->wall->setCurrValue("CZK", 100.0);
        $this->assertSame(188.47, round($this->wall->getBallance("EUR"), 2));
        $this->assertSame(222.73, round($this->wall->getBallance("USD"), 2));
        $this->assertSame(4900.0, round($this->wall->getBallance("CZK"), 2));

    }

}

?>