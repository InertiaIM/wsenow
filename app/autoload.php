<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\ClassLoader\UniversalClassLoader;
use Symfony\Component\ClassLoader\MapClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Symfony'          => array(__DIR__.'/../vendor/symfony/src', __DIR__.'/../vendor/bundles'),
    'Sensio'           => __DIR__.'/../vendor/bundles',
    'JMS'              => __DIR__.'/../vendor/bundles',
    'Doctrine\\Common' => __DIR__.'/../vendor/doctrine-common/lib',
    'Doctrine\\DBAL'   => __DIR__.'/../vendor/doctrine-dbal/lib',
    'Doctrine'         => __DIR__.'/../vendor/doctrine/lib',
    'Monolog'          => __DIR__.'/../vendor/monolog/src',
    'Assetic'          => __DIR__.'/../vendor/assetic/src',
    'Metadata'         => __DIR__.'/../vendor/metadata/src',
    'BeSimple'         => __DIR__.'/../vendor',
    'Knp'              => __DIR__.'/../vendor/bundles',
    'MZ'               => __DIR__.'/../vendor/bundles',
));
$loader->registerPrefixes(array(
    'Twig_Extensions_' => __DIR__.'/../vendor/twig-extensions/lib',
    'Twig_'            => __DIR__.'/../vendor/twig/lib',
));

// intl
if (!function_exists('intl_get_error_code')) {
    require_once __DIR__.'/../vendor/symfony/src/Symfony/Component/Locale/Resources/stubs/functions.php';

    $loader->registerPrefixFallbacks(array(__DIR__.'/../vendor/symfony/src/Symfony/Component/Locale/Resources/stubs'));
}

$loader->registerNamespaceFallbacks(array(
    __DIR__.'/../src',
));
$loader->register();

$mapLoader = new MapClassLoader([
    'ProxySettings' => __DIR__.'/../vendor/salesforce/soapclient/ProxySettings.php',
    'SforceBaseClient' => __DIR__.'/../vendor/salesforce/soapclient/SforceBaseClient.php',
    'SforceEmail' => __DIR__.'/../vendor/salesforce/soapclient/SforceEmail.php',
    'SforceEnterpriseClient' => __DIR__.'/../vendor/salesforce/soapclient/SforceEnterpriseClient.php',
    'SforceFieldTypes' => __DIR__.'/../vendor/salesforce/soapclient/SforceFieldTypes.php',
    'SforceHeaderOptions' => __DIR__.'/../vendor/salesforce/soapclient/SforceHeaderOptions.php',
    'SforceMetadataClient' => __DIR__.'/../vendor/salesforce/soapclient/SforceMetadataClient.php',
    'SforceMetaObject' => __DIR__.'/../vendor/salesforce/soapclient/SforceMetaObject.php',
    'SforcePartnerClient' => __DIR__.'/../vendor/salesforce/soapclient/SforcePartnerClient.php',
    'SforceProcessRequest' => __DIR__.'/../vendor/salesforce/soapclient/SforceProcessRequest.php',
]);
$mapLoader->register();

AnnotationRegistry::registerLoader(function($class) use ($loader) {
    $loader->loadClass($class);
    return class_exists($class, false);
});
AnnotationRegistry::registerFile(__DIR__.'/../vendor/doctrine/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php');

// Swiftmailer needs a special autoloader to allow
// the lazy loading of the init file (which is expensive)
require_once __DIR__.'/../vendor/swiftmailer/lib/classes/Swift.php';
Swift::registerAutoload(__DIR__.'/../vendor/swiftmailer/lib/swift_init.php');

