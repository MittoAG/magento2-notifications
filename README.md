## Mitto Notifications Module for Magento 2 
### 1. Requirements

+ **Magento 2.2.0 or greater**
+ **Composer PHP Dependency Manager**

### 2. Module installation

+ Open command prompt, go to `<MAGENTO_ROOT>` folder and run the following
commands:

```
composer require mitto-ag/magento2-notifications
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
php bin/magento cache:clean
php bin/magento cache:flush
```

### 3. Module configuration

+ Login to the store admin panel.
+ Navigate to `Stores` > `Configuration` > `Mitto` > `Notifications`.
+ Configuration is divided into two groups, `Customer` and `Admin`.
+ In each group there is a list of events that can be tied to a template added by `Mitto_Core` module.