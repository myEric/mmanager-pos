<?php

declare(strict_types=1);

namespace MmanagerPOS\Provider;

use Aura\Intl\TranslatorLocatorFactory;
use Aura\Intl\Package;

class Aura
{
    private $locale;

    public function __construct($locale) {
        $this->locale = $locale;
    }
    public function translate($message)
    {
        $factory = new TranslatorLocatorFactory();
        $translators = $factory->newInstance();

        $translators->setLocale($this->locale);

        // get the package locator
        $packages = $translators->getPackages();

        // place into the locator for Vendor.Package
        $packages->set('Vendor.Package', $this->locale, function() {
            // create a US English message set
            $package = new Package;
            $file = require APP_ROOT .'resources/locales/'.trim($this->locale).'/application.php';
            $package->setMessages($file);
            return $package;
        });

        // recall that the default locale is pt_BR
        $translator = $translators->get('Vendor.Package');
        return $translator->translate($message);
    }
}
