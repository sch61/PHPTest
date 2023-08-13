<?php
namespace Andrey\PHPTest;

/**
 * Summary of Wallet
 *  
 */
class Wallet
{
    protected string $owner;
    protected array $accounts;
    protected $rates;

    /**
     * Summary of __construct
     * @param string $name
     * @param array $rates
     * 
     * $rates - array of exchange rates relative to the main currency.
     * Main currency must have rate = 1.0 
     */
    function __construct(string $name, array $rates)
    {
        $this->owner = $name;
        $this->rates = &$rates;
        $this->accounts["CZK"] = 0.0;
    }

    /**
     * Summary of getOwner
     * @return string
     */
    public function getOwner(): string
    {
        return $this->owner;
    }

    /**
     * Summary of getCurrRate
     * @param string $curr_id
     * @return float
     */
    function getCurrRate(string $curr_id): float
    {
        return $this->rates[$curr_id];
    }

    /**
     * Summary of getExchangeRate
     * @param string $CurrFrom
     * @param string $CurrTo
     * @return float
     * 
     * Return exchange rate two currencies
     */
    function getExchangeRate(string $currFrom, string $currTo): float
    {
        if (
            array_key_exists($currFrom, $this->rates) && $this->rates[$currFrom] > 0 &&
            array_key_exists($currTo, $this->rates) && $this->rates[$currTo] > 0
        ) {
            $r = $this->rates[$currFrom] / $this->rates[$currTo];
        } else {
            $r = -1.0;
        }
        return $r;
    }

    /**
     * Summary of setCurrValue
     * @param string $curr_id
     * @param float $value
     * @return float
     * 
     * Sets the currency account to the specified value
     */
    function setCurrValue(string $curr_id, float $value): float
    {
        if ($value >= 0) {
            $this->accounts[$curr_id] = $value;
            return $this->accounts[$curr_id];
        } else {
            return -1.0;
        }
    }

    /**
     * Summary of getCurrValue
     * @param string $curr_id
     * @return float
     * 
     * Returns the balance of the specified currency account
     */
    function getCurrValue(string $curr_id): float
    {
        return $this->accounts[$curr_id];
    }

    /**
     * Summary of getCurrencies
     * @return array
     * 
     * Returns an array of currencies in the wallet
     */
    function getCurrencies(): array
    {
        return array_keys($this->accounts);
    }

    /**
     * Summary of changeCurrByValue
     * @param string $curr_id
     * @param float $val
     * @return float
     * 
     * Changes the currency account by the specified amount. Returns the new account balance.
     * If a negative balance is expected as a result, the transaction is not performed,
     * the balance is not changed. Returns -1.
     */
    function changeCurrByValue(string $curr_id, float $val): float
    {
        if (!array_key_exists($curr_id, $this->accounts)) {
            $this->accounts[$curr_id] = 0.0;
        }
        $res = $this->accounts[$curr_id] + $val;
        if ($res >= 0) {
            $this->accounts[$curr_id] = $res;
            return $res;
        } else {
            return -1.0;
        }
    }

    /**
     * Summary of currencyExchange
     * @param string $currFrom
     * @param string $currTo
     * @param float $val
     * @return float
     * 
     * Performs currency exchange.
     * The source currency account is reduced by the specified amount. 
     * The account of the receiving currency is increased by the amount
     * calculated according to the exchange rate.
     * Returns the new value of the receiving account.
     * The new value of the source account cannot become negative.
     * The array of rates must contain the rates of both currencies. 
     * Otherwise, the operation is not performed. Returns -1.
     */
    function currencyExchange(string $currFrom, string $currTo, float $val): float
    {
        if ($val >= 0 && $this->accounts[$currFrom] - $val > 0) {
            $r = $this->getExchangeRate($currFrom, $currTo);
            if ($r < 0) {
                return -1.0;
            }

            if (!array_key_exists($currTo, $this->accounts)) {
                $this->accounts[$currTo] = 0.0;
            }
            $this->accounts[$currTo] += round($val * $r, 2);
            $this->accounts[$currFrom] -= $val;
            return $this->accounts[$currTo];
        } else {
            return -2.0;
        }
    }

    /**
     * Summary of calcExchange
     * @param string $currFrom
     * @param string $currTo
     * @param float $val
     * @return float
     * 
     * Performs currency exchange calculations.
     * Returns the calculated value in the destination currency.
     * The array of rates must contain the rates of both currencies. 
     * Otherwise, the operation is not performed. Returns -1.
     */
    function calcExchange(string $currFrom, string $currTo, float $val): float
    {
        $r = $this->getExchangeRate($currFrom, $currTo);
        if ($r < 0) {
            return -1.0;
        }
        if ($val >= 0) {
            $ret = round($val * $r, 2);
            return $ret;
        } else {
            return -1.0;
        }
    }

    /**
     * Summary of getBallance
     * @param string $curr_id
     * @return float
     * 
     * Returns the total balance of the wallet in the specified currency.
     */
    function getBallance(string $curr_id): float
    {
        $ret = 0.0;
        foreach ($this->getCurrencies() as $curr) {
            $val = $this->getCurrValue($curr);
            if ($curr_id == $curr) {
                $ret += $val;
            } else {
                $ret += $this->calcExchange($curr, $curr_id, $val);
            }
        }
        return $ret;
    }
}

?>