<?php

namespace Tests;

trait XPathHelpers
{
    /**
     * ```
     * x("class", "button", "Złóż zapytanie")
     * x("role", "service-button", "Podkłady")
     * ```
     */
    private static function x(string $prop, string $outer_class, string $inner_text)
    {
        return implode("", [
            "//",
            "*[contains(@$prop, '$outer_class')]",
            "//",
            "*[contains(text(), '$inner_text')]"
        ]);
    }
}
