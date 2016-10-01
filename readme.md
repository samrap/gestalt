# Gestalt

[![StyleCI](https://styleci.io/repos/67276253/shield?style=flat)](https://styleci.io/repos/67276253)
[![Build Status](https://travis-ci.org/samrap/gestalt.svg?branch=master)](https://travis-ci.org/samrap/gestalt)
[![Latest Stable Version](https://poser.pugx.org/samrap/gestalt/v/stable)](https://packagist.org/packages/samrap/gestalt)
[![Total Downloads](https://poser.pugx.org/samrap/gestalt/downloads)](https://packagist.org/packages/samrap/gestalt)
[![Latest Unstable Version](https://poser.pugx.org/samrap/gestalt/v/unstable)](https://packagist.org/packages/samrap/gestalt)

> **ge·stalt (n):** _Something that is made of many parts and yet is somehow more than or different from the combination of its parts; broadly : the general quality or character of something._

Gestalt is a simple and elegant PHP package for managing your framework's configuration values. It is lightweight, flexible, framework agnostic, and has no dependencies other than PHP itself.

### Features
- **Lightweight:** Gestalt is built to be lightweight. No dependencies, no bloat, just an object-oriented wrapper around your framework's configuration.
- **Powerful:** Who said lightweight means powerless? Gestalt has a small footprint but packs a mean punch. Just take a look at its [Custom Loaders](https://github.com/samrap/gestalt-docs/blob/master/loaders.md) and [Observers](https://github.com/samrap/gestalt-docs/blob/master/observers.md) and you'll see for yourself.
- **Flexible:** Developers like to do things _our_ way. Gestalt gives you the flexibility to integrate seamlessly with how you store your configuration values.
- **Expressive syntax**: With its clean, collection-like syntax, code artisans will feel right at home. Not to worry messy developers, you'll like it too!

### Examples

**Basic Usage**

```php
$config = new Configuration([
    'app' => [
        'debug' => true,
        'version' => '1.0',
    ],
]);

// Get values using dot notation or ArrayAccess.
$config->get('app.debug');
$config['app'];

// Add values using dot notation or ArrayAccess.
$config->add('app.locale', 'en');
$config['mail'] = ['driver' => 'MailMonkey'];
```

**Custom Loading**

```php
$config = Configuration::load(new JsonFileLoader);

$config->get('app.debug');
```

**Observers**

```php
$config = new Configuration($values);

$config->attach(new StatefulObserver);

// Notifies the StatefulObserver that the
// Configuration has been updated.
$config->set('app.debug', false);
```

Interested? [Check out the docs](https://github.com/samrap/gestalt-docs) to see all of the features in action!
