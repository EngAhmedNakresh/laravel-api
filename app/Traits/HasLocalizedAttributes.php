<?php

namespace App\Traits;

trait HasLocalizedAttributes
{
    public function getLocalized(string $baseAttribute, ?string $lang = null): ?string
    {
        $locale = in_array($lang, ['ar', 'en'], true) ? $lang : app()->getLocale();
        $localizedAttribute = "{$baseAttribute}_{$locale}";

        return $this->{$localizedAttribute} ?? $this->{"{$baseAttribute}_en"} ?? $this->{"{$baseAttribute}_ar"} ?? null;
    }
}
