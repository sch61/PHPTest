<?php
/*
Cílem zadání je navržení a implementace tříd (např. Wallet, Bank, Money, Operation), rozhraní a unit testů pro podporu více měn v hypotetickém
systému (eshopu).
Řešení by mělo umět provádět:
1. základní operace s částkami (sčítání, odčítání, násobení, dělení) např.:
a. 100 CZK + 1 EUR = 126 CZK při kurzu 26 CZK za 1 EUR
b. 100 CZK * 5 = 500 CZK
2. porovnání částek
3. zaokrouhlování částek
4. směna měny

*/

require_once 'Wallet.php';

class CurrencyAccount
{
    protected string $currency_id;
    protected float $currency_value;
}

$rates = [
    "CZK" => 1.0,
];

$f = fopen("rates.csv","r");
if ($f != false) {
    while (!feof($f)) {
        $s = fgets($f, 100);
        $a = str_getcsv($s);
        if (count($a) != 2) {
            echo "Error in rates.csv";
        } else {
            $rates[$a[0]] = $a[1];
        }
    }
    fclose(($f));    
} else {
    echo "rates.csv no found\n";
    return;
}

echo "Rates of currencies today:\n";

foreach ($rates as $code => $rate) {
    echo "1 {$code} -> {$rate} CZK\n";
}
echo "==========================\n";

$wall = new Wallet("Shchukin");
echo "Hi, {$wall->owner}!\n";
echo "=========================\nOperations:\n";

if ($wall->setCurrValue("CZK", 2000.0) >= 0) {
    echo "New value CZK is {$wall->getCurrValue("CZK")}\n";
} else {
    echo "Error changing\n";
}


if ($wall->changeCurrByValue("CZK", 2000.0) >= 0) {
    echo "New value CZK is {$wall->getCurrValue("CZK")}\n";
} else {
    echo "Error changing\n";
}

if ($wall->changeCurrByValue("CZK", -5000.0) >= 0) {
    echo "New value CZK is {$wall->getCurrValue("CZK")}\n";
} else {
    echo "Error changing\n";
}

if ($wall->changeCurrByValue("EUR", 8000.0) >= 0) {
    echo "New value CZK is {$wall->getCurrValue("EUR")}\n";
} else {
    echo "Error changing\n";
}

echo "=========================\nExchange:\n";
if ($wall->currencyExchange("EUR", "USD", 3000, $rates["EUR"] / $rates["USD"]) >= 0) {
    echo "New value EUR {$wall->getCurrValue("EUR")}\n";
    echo "New value USD {$wall->getCurrValue("USD")}\n";
}

echo "=========================\nYour wallet:\n";
$ballance = 0.0;
foreach ($wall->getCurrencies() as $curr_id) {
    $val = $wall->getCurrValue($curr_id);
    echo "{$curr_id} {$val}\n";
    $ballance += round($val * $rates[$curr_id], 2);
}
echo "Total: {$ballance} CZK\n";

?>