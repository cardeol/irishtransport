# Irish Transport

<p>My intention is to provide a common interface for the transportation in Ireland in PHP ussing better techniques to avoid the overload of the original API's.</p>


### Requisites

```
Composer
Slim
Apache (running a virtualhost)
```

### Installation

```
git clone blablabla...
curl -sS https://getcomposer.org/installer | php
composer install
```

### Running Tests with PHPunit
linux: ./runtests.sh

### API (Routes powered by slim)

```
Station and geo info:
http://yourdomain/irishrail/getstations
http://yourdomain/dublinbus/getstations
http://yourdomain/luas/getstations

Train/Tram/Businfo:
http://yourdomain/irishrail/stationinfo/stationcode
http://yourdomain/dublinbus/stationinfo/stopid
http://yourdomain/luas/stationinfo/stationcode
```

# License

The content of this project itself is licensed under the
[Creative Commons Attribution 3.0 license](http://creativecommons.org/licenses/by/3.0/us/deed.en_US),
and the underlying source code used to format and display that content
is licensed under the [MIT license](http://opensource.org/licenses/mit-license.php).


