<?php

namespace App\Helpers;

use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslationHelper
{
    public static function TranslateText($text)
    {
        $locale = app()->getLocale();

        if ($locale == "fr" || empty($text)) {
            return $text;
        }

        $cacheKey = 'translation_' . md5($text . '_' . $locale);

        return cache()->remember($cacheKey, now()->addDays(7), function () use ($text, $locale) {
            $tr = new GoogleTranslate($locale);
            return $tr->translate($text);
        });
    }
}
