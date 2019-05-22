<?php

declare(strict_types=1);

use MmanagerPOS\Provider\Doctrine;
use MmanagerPOS\Provider\Slim;
use MmanagerPOS\Provider\Plates;
use MmanagerPOS\Provider\MRbac;
use MmanagerPOS\Provider\Monolog;
use MmanagerPOS\Provider\PhpRenderer;
use MmanagerPOS\Provider\Session;
use Slim\Container;
use Gettext\Translations;
use Gettext\Translator;

require_once __DIR__ . '/vendor/autoload.php';

// Define root path
defined('DS') ?: define('DS', DIRECTORY_SEPARATOR);
defined('APP_ROOT') ?: define('APP_ROOT', __DIR__. DS);

if (!file_exists(APP_ROOT . '/config/config.php')) {
    copy(APP_ROOT . '/config_example.php', APP_ROOT . '/config/config.php');
}

// Load .env file
if (!file_exists(APP_ROOT . '.env')) {
	copy(APP_ROOT . '.env.example', APP_ROOT . '.env');
}

$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

$locale = getenv('LC_MESSAGES');

if (!file_exists(__DIR__.'/resources/locales/'.$locale.'/LC_MESSAGES/'.$locale.'.po')) {
	copy(__DIR__.'/resources/locales/application.po', __DIR__.'/resources/locales/'.$locale.'/LC_MESSAGES/'.$locale.'.po');
}
//import from the language .po file:
$translations = Translations::fromPoFile(__DIR__.'/resources/locales/'.$locale.'/LC_MESSAGES/'.$locale.'.po');

//export to a php array:
if (!file_exists(__DIR__.'/resources/locales/'.$locale.'/LC_MESSAGES/'.$locale.'.php')) {

	$translations->toPhpArrayFile(__DIR__.'/resources/locales/'.$locale.'/LC_MESSAGES/'.$locale.'.php');
}
//and to a .mo file
if (!file_exists(__DIR__.'/resources/locales/'.$locale.'/LC_MESSAGES/'.$locale.'.mo')) {
	$translations->toMoFile(__DIR__.'/resources/locales/'.$locale.'/LC_MESSAGES/'.$locale.'.mo');
}
//Export to a json file
if (!file_exists(__DIR__.'/resources/locales/'.$locale.'/LC_MESSAGES/'.$locale.'.json')) {
	$translations->toJsonFile(__DIR__.'/resources/locales/'.$locale.'/LC_MESSAGES/'.$locale.'.json');
}

//Create the translator instance
$t = new Translator();

//Load your translations (exported as PhpArray):
$t->loadTranslations(__DIR__.'/resources/locales/'.$locale.'/LC_MESSAGES/'.$locale.'.php');

// To use global functions:
$t->register();

$cnt = new Container(require __DIR__ . '/config/config.php');

$cnt->register(new Doctrine())
	->register(new Plates())
	->register(new MRbac())
	->register(new Monolog())
	->register(new PhpRenderer())
	->register(new Session())
    ->register(new Slim());
return $cnt;
