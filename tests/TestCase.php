<?php

use Gestalt\Configuration;

abstract class TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Default configuration items to test with.
     *
     * @var array
     */
    private $configurationItems = [
        'app' => [
            'version' => '1.0',
            'debug' => true,
            'locale' => 'en',
        ],
        'database' => [
            'default' => 'mysql',
            'drivers' => [
                'mysql' => [
                    'database' => 'gestalt',
                    'username' => 'sam',
                    'password' => 'angostura',
                ],
                'sqlite' => [
                    'database' => 'gestalt',
                ],
            ],
        ],
    ];

    /**
     * Get the test configuration items.
     *
     * @return array
     */
    protected function getConfigurationItems()
    {
        return $this->configurationItems;
    }

    /**
     * Get a mocked \Gestalt\Util\ObserverInterface implementation.
     *
     * @param  int $maxUpdates
     * @param  int $minUpdates
     * @return \Mockery
     */
    protected function getObserver($maxUpdates = 0, $minUpdates = 1)
    {
        $observer = Mockery::mock('Gestalt\Util\ObserverInterface');

        if ($maxUpdates > 0) {
            $observer->shouldReceive('update')
                    ->atLeast()
                    ->times($minUpdates)
                    ->atMost($maxUpdates)
                    ->times($maxUpdates);
        } else {
            $observer->shouldReceive('update')->atLeast()->times($minUpdates);
        }

        return $observer;
    }

    /**
     * Return test suite to its original state.
     *
     * @return void
     */
    public function tearDown()
    {
        Mockery::close();
    }
}
