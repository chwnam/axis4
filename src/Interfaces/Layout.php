<?php


namespace Changwoo\Axis\Interfaces;


use Changwoo\Axis\Container;

interface Layout
{
    public function getContainer(): Container;

    public function getMainFile(): string;

    public function getSlug(): string;

    public function setSlug(string $slug);

    public function getVersion(): string;

    public function setVersion(string $version);

    public function getTextdomain(): string;

    public function setTextdomain(string $textdomain);

    public function getDefaultPriority(): int;

    public function setDefaultPriority(int $priority);

    public function includeFile(string $relpath);

    public function setRootModules(array $modules);

    public function standby();

    public function addDefaultHooks();
}