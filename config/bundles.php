<?php

$bundles = [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle::class => ['all' => true],
    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],
    Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class => ['all' => true],
    Symfony\Bundle\SecurityBundle\SecurityBundle::class => ['all' => true],
    Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
    Symfony\Bundle\WebProfilerBundle\WebProfilerBundle::class => ['dev' => true, 'test' => true],
    Symfony\Bundle\MonologBundle\MonologBundle::class => ['all' => true],
    Symfony\WebpackEncoreBundle\WebpackEncoreBundle::class => ['all' => true],
    DAMA\DoctrineTestBundle\DAMADoctrineTestBundle::class => ['test' => true],
];
if (in_array($this->getEnvironment(), ['dev', 'test'])) {
    $moreBundles = [
        Symfony\Bundle\DebugBundle\DebugBundle::class => ['dev' => true, 'test' => true],
        Symfony\Bundle\MakerBundle\MakerBundle::class => ['dev' => true],
        Symfony\Bundle\WebServerBundle\WebServerBundle::class => ['dev' => true]
    ];

    $bundles = array_merge($bundles, $moreBundles);
}

return $bundles;
