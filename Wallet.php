<?php
class Wallet
{
    public string $owner;
    protected array $accounts;
    function __construct(string $name)
    {
        $this->owner = $name;
        $this->setCurrValue("CZK", 0.0);
    }
    function setCurrValue(string $curr_id, float $value): float
    {
        if ($value >= 0) {
            $this->accounts[$curr_id] = $value;
            return $this->accounts[$curr_id];
        } else {
            return -1.0;
        }
    }
    function getCurrValue(string $curr_id): float
    {
        return $this->accounts[$curr_id];
    }
    function getCurrencies(): array
    {
        return array_keys($this->accounts);
    }

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
            return -1;
        }
    }
    function currencyExchange(string $currFrom, string $currTo, float $val, $rate): float
    {
        if ($val >= 0 && $this->accounts[$currFrom] - $val > 0) {
            $this->accounts[$currFrom] -= $val;
            if (!array_key_exists($currTo, $this->accounts)) {
                $this->accounts[$currTo] = 0.0;
            }
            $this->accounts[$currTo] += round($val * $rate, 2);
            return $this->accounts[$currTo];
        } else {
            return -1;
        }
    }
}

?>