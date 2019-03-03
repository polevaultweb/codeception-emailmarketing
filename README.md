Codeception Drip
==========

An abstract email marketing module for Codeception.

## Installation
You need to add the repository into your composer.json file

```bash
    composer require --dev polevaultweb/codeception-emailmarketing
```

## Usage

In your specific email marketing module, simply extend from `Email Marketing`.

### Supports

* getTagssForSubscriber
* getActiveCampaignsForSubscriber
* deleteSubscriber

And assertions

* seeCustomFieldForSubscriber
* seeTagsForSubscriber
* cantSeeTagsForSubscriber
* seeCampaignsForSubscriber
* cantSeeCampaignsForSubscriber
* waitForSubscriberToNotHaveTags

### Usage

```php
$I = new AcceptanceTester( $scenario );

$I->seeTagsForSubscriber( 'john@gmail.com', array( 'customer', 'product-x' ) );
$I->seeCampaignsForSubscriber( 'john@gmail.com', array( 12345, 67890 ) );

```

