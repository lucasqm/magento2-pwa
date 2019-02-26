>Ignore my awful english!

# Magento 2 PWA

This a simple module to make your site PWA.

## Installation

You need modify your Nginx configuration file for the module to work.

Server configuration...
```nginx
# service worker location
location ~*\/(sw|OneSignalSDKUpdaterWorker|OneSignalSDKWorker)\.js {
    alias $MAGE_ROOT/$1.js;
}
```

Add this in media location...

```nginx
add_header 'Service-Worker-Allowed' '/';
```

## Configurations
All the configurations are in _**Store > Configuration > Resultate > PWA Configs**_

### Configure your manifest

Configure your **_manifest.json_**.

Add the tags and values...

E.g.:

| Tags              | Values            |
| ----------------- | ----------------- |
| short_name        | My Site           |
| name              | My Site Full Name |
| start_ur          | /                 |
| background_color  | #FFFFFF           |
| theme_color       | #FFFFFF           |
| display           | standalone        |

**_You can upload your icons in the configurations, but you need save to upload the file!_**

**_All the files uploaded are listed in the configuration!_**

Add your tag icons...

E.g.:

| SRC                                            | Type      | Sizes   |
| ---------------------------------------------- | --------- | ------- |
| /media/pwa/manifest/icons/default/icon-192.png | image/png | 192x192 |
| /media/pwa/manifest/icons/default/icon-512.png | image/png | 512x512 |

**_After save, click on button to generate manifest.json_**

### Configure your Service Worker

Add your pre cached routes...

E.g.:

| Route |
| ----- |
| /     |
| /cat1 |
| /cat2 |
| /cat2 |

Configure your cache max age...

**_In this field you can select 1-7 days._**

Add your cache prefix...

E.g.:
```text
my-site-prd
```

Add your cache suffix...

**_If empty, the value is the deploy version._**

E.g.:
```text
v1
```

### Configure [OneSignal](https://onesignal.com)

This configuration is if you wants add the [OneSignal](https://onesignal.com) in your site.

Add your appId...

E.g.:
```text
1234asd-asd1234-1234asd-asd1234
```
