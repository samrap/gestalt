<?php

use Gestalt\Configuration;

class ConfigurationTest extends PHPUnit_Framework_TestCase
{
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
}
