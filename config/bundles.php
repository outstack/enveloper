<?php

$bundles = [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Symfony\Bundle\SecurityBundle\SecurityBundle::class => ['all' => true],
    Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
    Symfony\Bundle\MonologBundle\MonologBundle::class => ['all' => true],
    Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle::class => ['all' => true],
    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],
    AppBundle\AppBundle::class => ['all' => true],

    Symfony\Bundle\DebugBundle\DebugBundle::class => ['dev' => true, 'test' => true],
    Symfony\Bundle\WebProfilerBundle\WebProfilerBundle::class => ['dev' => true, 'test' => true]
];

// Allow extra bundles to be added via environment variables
foreach (array_map('trim', explode(',', getenv('ENVELOPER_EXTENSIONS'))) as $extensionClass) {
    if (class_exists($extensionClass)) {
        $bundles[$extensionClass] = ['all' => true];
    }
}

return $bundles;