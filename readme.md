# Gestalt

[![StyleCI](https://styleci.io/repos/67276253/shield?style=flat)](https://styleci.io/repos/67276253)
[![Build Status](https://travis-ci.org/samrap/gestalt.svg?branch=master)](https://travis-ci.org/samrap/gestalt)
[![Latest Stable Version](https://poser.pugx.org/samrap/gestalt/v/stable)](https://packagist.org/packages/samrap/gestalt)
[![Total Downloads](https://poser.pugx.org/samrap/gestalt/downloads)](https://packagist.org/packages/samrap/gestalt)
[![Latest Unstable Version](https://poser.pugx.org/samrap/gestalt/v/unstable)](https://packagist.org/packages/samrap/gestalt)

#### ge·stalt (n)
> _Something that is made of many parts and yet is somehow more than or different from the combination of its parts; broadly : the general quality or character of something._

Gestalt is a simple, elegant PHP package for managing your framework's configuration values. It is lightweight, flexible, framework agnostic, and has no dependencies other than PHP itself.

What makes Gestalt unique is its expressive collection-like syntax and the ability to define and load configuration values in virtually any fashion via a common interface.

```php
// Create a new Configuration object with an array of values...
$configuration = new Configuration(['debug' => true]);

// Or, instantiate the Configuration object with a LoaderInterface...
$loader = new CustomLoader;
$configuration = Configuration::fromLoader($loader);

// Once the Configuration object is created, using it is a breeze...
$development = $configuration->get('debug');
$configuration->set('debug', false);

// We can even use dot notation for nested arrays:
$mailDriver = $configuration->get('mail.driver');

// Or, access the Configuration object as an array:
$appConfig = $configuration['app'];
```

As you can see, Gestalt is sweet and simple to play with right out of the box. Its expressive syntax makes retrieving and setting values a breeze, while its custom loader functionality provides complete freedom in how you define your framework's configuration values.

## Installation
Install via Composer:

`composer require samrap/gestalt`

## Usage

#### The Basics
You can create a new Configuration instance by simply passing an associative array of configuration values to the object's constructor:

```php
use Gestalt\Configuration;

$config = new Configuration([
    'debug' => false,
    'name' => 'Sam Rapaport',
]); 
```

Once the object has been created, we can easily access its values using either the `get` method or accessing the object as an array:

```php
// "Sam Rapaport"
echo $config->get('name');

// "Sam Rapaport"
echo $config['name'];
```

Using the `get` method, we can use "dot notation" for accessing nested values. In the following example, the `$debug` variable will be set to `true`:

```php
$config = new Configuration([
    'app' => [
        'debug' => true,
        'logfile' => 'storage/app.log',
    ],
]);

$debug = $config->get('app.debug');
```

We can add new items to the configuration object using the `add` method:

```php
$config->add('mail', ['driver' => 'MailMonkey']);
```

The `add` method will only update the Configuration object if the configuration item does not already exist. To overwrite an existing configuration value, use the `set` method instead:

```php
$config->set('debug', false);
```

At some point you may wish to reset the Configuration object back to the original values it was created with. This can be done at any time by using the object's `reset` method:

```php
$config->reset();
```

The `reset` method returns an instance of itself, allowing you chain additional method calls:

```php
$config->reset()->add('debug', true);
```

#### Custom Loaders
So far in the examples, we have been creating our Configuration object by passing in an array to its constructor. While this may work for smaller applications and frameworks, it is likely you have a more robust way of storing your configuration values. Gestalt is built to handle such cases thanks to it's custom loaders:

```php
$loader = new PhpDirectoryLoader;
$config = Configuration::fromLoader($loader);
```

Let's take a look at defining a custom loader and how it can help us create a Configuration object more dynamically:

```php
namespace App\Configuration;

use Gestalt\Loaders\LoaderInterface;

class PhpFileLoader implements LoaderInterface
{
    /**
     * Configuration files to load.
     * 
     * @var array
     */
    $this->files = [
        'app' => '/config/app.php',
        'mail' => '/config/mail.php',
    ];

    /**
     * Load the configuration items and return them as an array.
     *
     * @param  array  $files
     * @return array
     */
    public function load()
    {
        $items = [];

        foreach ($files as $name => $file) {
            $items[$name] = require $file;
        }

        return $items;
    }
}
```

All loaders must implement the `Gestalt\Loaders\LoaderInterface` interface and its `load` method. In `load`, we simply grab the configuration values from wherever they are stored (PHP files in this case) and return an array representation of them.

Now that we have defined how our loader will get the configuration values, we can simply pass an instance to the `Configuration::fromLoader` method to retrieve a new Configuration instance:

```php
$loader = new PhpFileLoader;
$config = Configuration::fromLoader($loader);

$app = $config->get('app');
$mail = $config->get('mail');
```

As you can see, using loaders can be quite powerful in conjunction with Gestalt's Configuration object. While you can create your own loaders, Gestalt ships with some prebuilt loaders right out of the box. They, along with the interface, live in the `Gestalt\Loaders` namespace.

#### Saving Configuration Changes
It is likely that your configuration settings are stored in some sort of _stateful_ manner. Whether it is a group of JSON files, YAML files, or even stored in the database, there may be a time when you wish to persist the changes made in your application to your stateful configuration. Gestalt has no working knowledge of where your configuration values come from, so it cannot do this directly, but it does help you quite a bit by using the [Observer Pattern](https://en.wikipedia.org/wiki/Observer_pattern).

Simply put, the Configuration object will notify any registered _observers_ whenever the `add` or `set` methods are called.

To register an observer, simply pass an observer instance to the Configuration's `attach` method:

```php
$config = new Configuration(['debug' => true]);
$config->attach(new ConfigurationObserver);
```

You can attach as many observers as you would like to the Configuration object. Whenever the `add` or `set` methods are called, each observer's `update` method will be called with the Configuration instance as the parameter.

You can also detach observers by calling the `detach` method on the Coniguration object:

```php
$config->detach($myObserver);
```

To create an observer, define a class that implements the `Gestalt\Util\ObserverInterface` interface. This interface requires one public method, `update`, which takes an `Observable` as its only parameter. Since the Configuration object extends the `Gestalt\Util\Observable` class, each observer will get the Configuration instance it is attached to and be able to perform stateful operations accordingly. Let's look at an example of a basic observer you might define in your app:

```php
...

use Gestalt\Util\Observable;
use Gestalt\Util\ObserverInterface;

class ConfigurationObserver implements ObserverInterface
{
    public function update(Observable $config)
    {
        $this->file->write($config->all());
    }
}

```

We implement the `ObserverInterface` and define its `update` method to write the values its argument to some file object. Once we register this observer with our Configuration object, the `update` method will be called whenever we `set` or `add` a value to the configuration.

Using this feature in conjuntion with Custom Loaders can be quite powerful. Let's look at a full example:

```php
$config = Configuration::fromLoader(new JsonFileLoader);
$config->attach(new JsonFileObserver);

// Add and set methods are called, JsonFileObserver's update
// method will be called with the updated configuration object.
$config->add('bar', 123);
$config->set('debug', true);
```

Note that calling the Configuration's `reset` method does not automatically notify the observers. However, since `reset` returns an instance of itself, we can chain the notify function like so:

```php
$config->reset()->notify();
```

## Conclusion
More documentation and features coming soon!

## Contribution
Contributions are more than welcome. Feel free to submit pull requests or add issues for additional features you would like to see!
