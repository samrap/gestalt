<?php

use Gestalt\Configuration;

class ConfigurationTest extends TestCase
{
    public function test_get_method_gets_item()
    {
        $c = new Configuration($this->getConfigurationItems());

        $this->assertArrayHasKey('debug', $c->get('app'));
    }

    public function test_get_method_gets_item_with_dot_notation()
    {
        $c = new Configuration($this->getConfigurationItems());

        $this->assertTrue($c->get('app.debug'));
        $this->assertEquals('1.0', $c->get('app.version'));
        $this->assertEquals('gestalt', $c->get('database.drivers.mysql.database'));
    }

    public function test_all_method_gets_all_items()
    {
        $items = $this->getConfigurationItems();
        $c = new Configuration($this->getConfigurationItems());

        $this->assertEquals($items, $c->all());
    }

    public function test_exists_method_verifies_existance()
    {
        $c = new Configuration($this->getConfigurationItems());

        $this->assertTrue($c->exists('app'));
    }

    public function test_add_method_adds_new_item()
    {
        $c = new Configuration($this->getConfigurationItems());
        $c->add('baz', 'bin');

        $this->assertEquals('bin', $c->get('baz'));
    }

    public function test_add_method_adds_new_item_with_dot_notation()
    {
        $c = new Configuration($this->getConfigurationItems());

        $c->add('app.foo', 123);
        $c->add('mail.driver', 'MailMonkey');

        $this->assertEquals(123, $c->get('app.foo'));
        $this->assertEquals('MailMonkey', $c->get('mail.driver'));
    }

    public function test_add_method_ignores_existing_items()
    {
        $c = new Configuration(['foo' => 'bar']);
        $c->add('foo', 'bin');
        $c->add('foo.bin', 123);

        $this->assertEquals('bar', $c->get('foo'));
        $this->assertNull($c->get('foo.bin'));
    }

    public function test_set_method_sets_item()
    {
        $c = new Configuration($this->getConfigurationItems());

        // The set method should allow overriding.
        $c->set('app', null);
        // The set method should also allow basic add functionality.
        $c->set('baz', 'bin');

        $this->assertNull($c->get('app'));
        $this->assertEquals('bin', $c->get('baz'));
    }

    public function test_set_method_sets_new_item_with_dot_notation()
    {
        $c = new Configuration($this->getConfigurationItems());

        $c->set('app.foo', 123);
        $c->set('app.debug', false);
        $c->set('mail.driver', 'MailMonkey');

        $this->assertArrayHasKey('version', $c->get('app'));
        $this->assertEquals('123', $c->get('app.foo'));
        $this->assertFalse($c->get('app.debug'));
        $this->assertEquals('MailMonkey', $c->get('mail.driver'));
    }

    public function test_remove_method_removes_item()
    {
        $c = new Configuration($this->getConfigurationItems());
        $c->remove('app');

        $this->assertNull($c->get('app'));
    }

    public function test_remote_method_removes_item_with_dot_notation()
    {
        $c = new Configuration($this->getConfigurationItems());
        $c->remove('app.version');

        $this->assertNull($c->get('app.version'));
    }

    public function test_load_method_loads_configuration()
    {
        $loader = Mockery::mock('\Gestalt\Loaders\LoaderInterface');
        $loader->shouldReceive('load')->andReturn($this->getConfigurationItems());
        $c = Configuration::fromLoader($loader);

        $this->assertEquals('1.0', $c->get('app.version'));
    }

    public function test_for_ArrayAccess_implementation()
    {
        $c = new Configuration($this->getConfigurationItems());

        $this->assertArrayHasKey('version', $c['app']);
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
        $items = $this->getConfigurationItems();
        $a = new Configuration($items);

        $a->set('app', null);
        $this->assertNull($a->get('app'));

        $a->reset();
        $this->assertEquals($items, $a->all());
    }

    public function test_configuration_is_observable()
    {
        $this->assertInstanceOf(
            'Gestalt\Util\Observable',
            new Configuration($this->getConfigurationItems())
        );
    }

    public function test_configuration_notifies_observers_on_add()
    {
        $c = new Configuration($this->getConfigurationItems());

        $c->attach($this->getObserver());
        $c->attach($this->getObserver());

        // If the mocked observers aren't notified, an error will be thrown.
        $c->add('foo', 123);
    }

    public function test_configuration_notifies_observers_on_set()
    {
        $c = new Configuration($this->getConfigurationItems());

        $c->attach($this->getObserver());
        $c->attach($this->getObserver());

        // If the mocked observers aren't notified, an error will be thrown.
        $c->set('foo', 123);
    }

    public function test_configuration_does_not_notify_observers_on_reset()
    {
        $c = new Configuration($this->getConfigurationItems());

        $c->attach($this->getObserver(1));
        $c->set('foo', 123);

        // Since we told the mocked observer it should only be updated once,
        // if the reset method notifies observers, we will get an error.
        $c->reset();
    }

    public function test_create_method_creates_configuration_from_closure_loader()
    {
        $values = $this->getConfigurationItems();
        $c = Configuration::create(function () use ($values) {
            return $values;
        });

        $this->assertEquals('1.0', $c->get('app.version'));
    }

    public function test_create_method_creates_configuration_from_class_loader()
    {
        $loader = Mockery::mock('\Gestalt\Loaders\LoaderInterface');
        $loader->shouldReceive('load')->andReturn($this->getConfigurationItems());
        $c = Configuration::create($loader);

        $this->assertEquals('1.0', $c->get('app.version'));
    }
}
