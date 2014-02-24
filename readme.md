# Twitter for Slack

A Laravel PHP package for building a Slack integration for Twitter.

## Install

Normal install via Composer.

### Providers

Register the service provider in your ``app/config/app.php`` file:

```php
'Thujohn\Twitter\TwitterServiceProvider',
'Roumen\Feed\FeedServiceProvider',
'Travis\Slack\Twitter\Provider',
```

Also, add the facades:

```php
'Twitter' => 'Thujohn\Twitter\TwitterFacade',
'Feed' => 'Roumen\Feed\Facades\Feed',
```

### Config

Copy the ``Thujohn\Twitter`` vendor config file to ``app/config/packages/thujohn/twitter/config.php`` and input the necessary information.

## Usage

Currently only supporting the search feature:

```
http://<YOURDOMAIN>/slack/twitter/search?q=<YOURQUERY>
```

Will cache results for 5 minutes.