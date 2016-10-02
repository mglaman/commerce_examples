Commerce Demo [![Build Status](https://travis-ci.org/mglaman/commerce_demo.svg?branch=master)](https://travis-ci.org/mglaman/commerce_demo)
===============================

A demo module for Commerce 2.x. Provides some usually defaults and a sample
migration.

## Installation

Ensure your Drupal installation has the following information in the `repositories`
section. This will allow Composer to discover this module (since it's not on Drupal.org)

```
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/mglaman/commerce_demo"
        }
    ],
```

Then, in your Drupal directory run `composer require drupal/commerce_demo:dev-master`.

## Features

* Sample product type
* Color and size attributes
* Sample product display and add to cart form configurations
* Migration example from local CSV source
* Price resolvers
* Order type resolvers

#### Price resolver

Visit any product and add `?discount=TRUE` to trigger a 15% discount.

#### Order type resolver

Based on the product variation type, the product will go into the default order type, or be treated as digital.
