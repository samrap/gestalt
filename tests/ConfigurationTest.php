<?php

use Gestalt\Configuration;

class ConfigurationTest extends PHPUnit_Framework_TestCase
{
    /**
     * Get a mocked Observer implementation.
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

    public function test_get_method_gets_item()
    {
        $c = new Configuration(['debug' => true, 'foo' => 'bar']);

        $this->assertTrue($c->get('debug'));
        $this->assertEquals('bar', $c->get('foo'));
    }

    public function test_get_method_gets_item_with_dot_notation()
    {
        $c = new Configuration([
            'app' => [
                'debug' => true,
                'url' => 'localhost',
                'foo' => ['bar' => 'baz'],
            ],
            'mail' => [
                'from' => 'sam@example.com',
                'reply-to' => 'dev@null.com',
            ],
        ]);

        $this->assertTrue($c->get('app.debug'));
        $this->assertEquals('baz', $c->get('app.foo.bar'));
        $this->assertEquals('sam@example.com', $c->get('mail.from'));
    }

    public function test_all_method_gets_all_items()
    {
        $items = ['app' => ['foo' => 'bar']];

        $c = new Configuration($items);

        $this->assertEquals($items, $c->all());
    }

    public function test_exists_method_verifies_existance()
    {
        $c = new Configuration(['foo' => 'bar']);

        $this->assertEquals('bar', $c->get('foo'));
        $this->assertEmpty($c->get('baz'));
    }

    public function test_add_method_adds_new_item()
    {
        $c = new Configuration(['foo' => 'bar']);
        $c->add('baz', 'bin');

        $this->assertEquals('bin', $c->get('baz'));
    }

    public function test_add_method_ignores_existing_item()
    {
        $c = new Configuration(['foo' => 'bar']);
        $c->add('foo', 'bin');

        $this->assertEquals('bar', $c->get('foo'));
    }

    public function test_set_method_sets_item()
    {
        $c = new Configuration(['foo' => 'bar']);
        $c->set('foo', 123);
        $c->set('baz', 'bin');

        $this->assertEquals(123, $c->get('foo'));
        $this->assertEquals('bin', $c->get('baz'));
    }

    public function test_remove_method_removes_item()
    {
        $c = new Configuration(['foo' => 'bar']);

        $c->remove('foo');

        $this->assertEmpty($c->all());
    }

    public function test_load_method_loads_configuration()
    {
        $loader = Mockery::mock('\Gestalt\Loaders\LoaderInterface');
        $loader->shouldReceive('load')->andReturn(['foo' => 'bar']);

        $c = Configuration::fromLoader($loader);

        $this->assertEquals('bar', $c->get('foo'));
    }

    public function test_for_ArrayAccess_implementation()
    {
        $c = new Configuration(['foo' => 'bar']);

        $this->assertEquals('bar', $c['foo']);
    }

    public function test_instantiation_from_different_types()
    {
        $items = ['foo' => 'bar'];
        $a = new Configuration($items);
        $b = new Configuration(new Configuration($items));
        $c = new Configuration(new ArrayIterator($items));

        $this->assertInternalType('array', $a->all());
        $this->assertInternalType('array', $b->all());
        $this->assertInternalType('array', $c->all());
        $this->assertEquals('bar', $a->get('foo'));
        $this->assertEquals('bar', $b->get('foo'));
        $this->assertEquals('bar', $c->get('foo'));
    }

    public function test_reset_method_resets_changes()
    {
        $items = ['foo' => 'bar'];
        $a = new Configuration($items);

        $a->set('foo', 123);
        $this->assertEquals(['foo' => 123], $a->all());

        $a->reset();
        $this->assertEquals($items, $a->all());
    }

    public function test_configuration_is_observable()
    {
        $this->assertInstanceOf('Gestalt\Util\Observable', new Configuration);
    }

    public function test_configuration_notifies_observers_on_add()
    {
        $c = new Configuration(['foo' => 123, 'bar' => 456]);

        $c->attach($this->getObserver());
        $c->attach($this->getObserver());

        $c->add('baz', 789);
    }

    public function test_configuration_notifies_observers_on_set()
    {
        $c = new Configuration(['foo' => 123, 'bar' => 456]);

        $c->attach($this->getObserver());
        $c->attach($this->getObserver());

        $c->set('baz', 789);
    }

    public function test_configuration_does_not_notify_observers_on_reset()
    {
        $c = new Configuration(['foo' => 123]);

        $c->attach($this->getObserver(1));
        $c->set('foo', 456);

        // Since we told the mocked observer it should only be updated once,
        // if the reset method notifies observers, we will get an error.
        $c->reset();
    }

    public function tearDown()
    {
        Mockery::close();
    }
}
