<?php

class Locale
{
    public string $languageCode;
    public string $countryCode {
        set(string $countryCode) {
            $this->countryCode = strtoupper($countryCode);
        }
    }
    public string $combinedCode {
        get => sprintf("%s_%s", $this->languageCode, $this->countryCode);
        set(string $value) {
            [$this->languageCode, $this->countryCode] = explode('_', $value, 2);
        }
    }

    public function __construct(string $languageCode, string $countryCode)
    {
        $this->languageCode = $languageCode;
        $this->countryCode = $countryCode;
    }
}

$locale = new Locale('pl', 'pl');
echo $locale->countryCode; // Wyświetli: PL echo $locale->combinedCode;