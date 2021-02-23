<?php


namespace Changwoo\Axis\Tests;

use Changwoo\Axis\ThemeLayout;
use WP_UnitTestCase;

class TestLayout extends WP_UnitTestCase
{
    public function test_loadRegisterFile()
    {
        $file = __DIR__ . '/registrables.php';
        if (file_exists($file)) {
            unlink($file);
        }

        $contents = <<< 'PHP_EOL'
<?php
$this->includeTest = true;
PHP_EOL;

        file_put_contents($file, $contents);

        $layout = (new ThemeLayout())
            ->setMainFile(__FILE__)
            ->loadRegisterFile('registrables');

        $this->assertTrue(isset($layout->includeTest) && $layout->includeTest);

        if (file_exists($file)) {
            unlink($file);
        }
    }
}