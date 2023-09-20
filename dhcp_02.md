# Настройка DHCP Failover и DHCP Relay

##### **Настройка первичного DHCP-сервера**

Отказоустойчивый DHCP-сервер строится по схеме первичного и вторичного узлов, которые могут работать как в режиме балансировки нагрузки (рекомендуется), так и в режиме горячего резерва. Отвечающие за это настройки мы рассмотрим ниже, а пока установим сам пакет ISC DHCP:

```
apt install isc-dhcp-server
```

Обязательно идем в файл **/etc/default/isc-dhcp-server**, здесь нас будут интересовать две последние опции в файле:

```
INTERFACESv4="eth0"
#INTERFACESv6=""
```

В первой из них нужно задать интерфейс (можно несколько) на которых ваш DHCP-сервер будет принимать запросы, это важно, если интерфейсов более одного. Вторая опция отвечает за работу c IPv6, если вы не работаете с этим протоколом и не настраивали обработку IPv6 запросов, то просто отключите шестую версию закомментировав эту строку.

После чего перейдем к редактированию конфигурационного файла, он в основном содержит некоторые общие параметры и закомментированные примеры, поэтому мы оставим в нем только общие настройки, а все остальные вынесем во внешние файлы. Откроем **/etc/dhcp/dhcpd.conf**, найдем и раскомментируем в нем следующую опцию:

```
authoritative;
```

Она включает "авторитетность" сервера, в этом случае получив запрос на адрес, не принадлежащий текущей сети, сервер ответит сообщением DHCPNAK, которое предлагает клиенту отказаться от адреса и запросить новый. Это позволяет быстрее получать адреса мобильным клиентам, которые до этого были подключены к другой сети.

Далее добавляем конфигурацию failover:

```shell
failover peer "failover-dhcp" {
  primary;
  address 10.10.10.30;
  port 519;
  peer address 10.10.10.31;
  peer port 519;
  max-response-delay 60;
  max-unacked-updates 10;
  mclt 3600;
  split 128;
  load balance max seconds 3;
}
```

Самой первой строкой мы указываем тип сервера - **primary** - первичный. Затем следует адрес и порт сервера, адрес и порт партнера. Для работы используется порт 647, который специально используется для DHCP-FAILOVER. опция **max-response-delay** указывает на максимально допустимое расхождение времени между двумя узлами.

Два следующих параметра должны быть указаны **только на первичном сервере**.

- **mclt** (*максимальное время обслуживания клиента*) - он показывает в течении какого времени сервер, находящийся в состоянии обработки отказа, будет ждать восстановления связи с партнером, по его истечении контроль за распределением IP-адресов полностью переходит под управление оставшегося сервера.
- **split** - задает параметры разделения пула адресов между серверами. Может иметь значения от 0 до 256, при значении в 128 пул будет разделен 50/50, и нагрузка будет равномерно балансироваться между серверами. Если указать 256, то весь пул будет управляться первичным сервером, а вторичный сервер перейдет в режим горячей замены.

После этого описываем в этом же конфиге подсети, из которых будут выдаваться адреса:

```shell
# VLAN 10
subnet 10.10.10.0 netmask 255.255.255.0 {
	option subnet-mask 255.255.255.0;
  	option routers 10.10.10.1;
 	option domain-name-servers 10.10.10.10;
  	default-lease-time 7200;
  	max-lease-time 86400;
  	pool {
  		failover peer "failover-dhcp";
    	range 10.10.10.100 10.10.10.254;
	}
}

# VLAN 20
subnet 10.10.20.0 netmask 255.255.255.0 {
	option subnet-mask 255.255.255.0;
 	option routers 10.10.20.1;
  	option domain-name-servers 10.10.10.10;
  	default-lease-time 7200;
  	max-lease-time 86400;
 	pool {
   		failover peer "failover-dhcp";
    	range 10.10.20.100 10.10.20.254;
	}
}
```

Настройки области предельно просты, мы предлагаем клиентам самый базовый набор опций: маску сети, адрес маршрутизатора и адрес(а) DNS-серверов.

Отдельно обратим внимание на опции **default-lease-time** - время аренды, выдаваемое по умолчанию и **max-lease-time** - максимальное время аренды, которое может быть выдано по запросу клиента. Если клиент не запрашивает конкретное время аренды ему будет выдан адрес на время, указанное в параметре по умолчанию, иначе - желаемое время, но не превышающее максимальное. В нашем случае это 8 часов и сутки.

В секции **pool** указываем диапазон адресов к выдаче и ссылку на отказоустойчивую группу. Если пулов несколько - то указываем отказоустойчивые группы для каждого из них, при этом разные пулы могут обслуживать разные пары серверов.

Проверим правильность конфигурации:

```shell
dhcpd -t -cf /etc/dhcp/dhcpd.conf
```

И при отсутствии ошибок обязательно перезагрузим сервер.

Для управления службой используйте

```shell
systemctl start|stop|restart|status isc-dhcp-server
```

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2022-11/scaled-1680-/rONimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2022-11/rONimage.png)

Убедившись, что служба запущена и работает без ошибок, перейдем к настройке второго сервера.

##### **Настройка вторичного DHCP-сервера**

Настройки обоих серверов должны быть полностью идентичны, за исключением настроек отказоустойчивой группы, поэтому можете выполнить все настройки аналогично предыдущей части статьи или просто скопировать конфигурационные файлы с первичного сервера на вторичный. Все изменения также должны вноситься синхронно. Поэтому мы и разделили конфигурацию на несколько внешних файлов: если вы внесли изменения в настройки области или добавили новые резервирования - просто скопируйте соответствующий файл с первичного сервера на вторичный.

Внести изменения нам потребуется только в файл **/etc/dhcp/dhcpd.d/failover.conf**, он должен иметь следующее содержимое:

```shell
failover peer "failover-dhcp" {
  secondary;
  address 10.10.10.31;
  port 520;
  peer address 10.10.10.30;
  peer port 519;
  max-response-delay 60;
  max-unacked-updates 10;
  load balance max seconds 3;
}
```

Первая строка указывает что это вторичный сервер - **secondary**, затем следуют адрес и порт сервера и его партнера, за ними остальные опции, которые остаются без изменений.

Сохраняем настройки, проверяем их и перезагружаем сервер. Теперь нашу область будут обслуживать сразу оба сервера, балансируя нагрузку между собой. Для проверки по очереди выключаем сервера и убеждаемся, что оставшийся сервер берет на себя обслуживание клиентов.

##### **Итоговая конфигурация для DHCP-серверов**

Основной сервер:

```shell
authoritative;
failover peer "failover-dhcp" {
  primary;
  address 10.10.10.30;
  port 519;
  peer address 10.10.10.31;
  peer port 519;
  max-response-delay 60;
  max-unacked-updates 10;
  mclt 3600;
  split 128;
  load balance max seconds 3;
}

# VLAN 10
subnet 10.10.10.0 netmask 255.255.255.0 {
	option subnet-mask 255.255.255.0;
  	option routers 10.10.10.1;
 	option domain-name-servers 10.10.10.10;
  	default-lease-time 7200;
  	max-lease-time 86400;
  	pool {
  		failover peer "failover-dhcp";
    	range 10.10.10.100 10.10.10.254;
	}
}

# VLAN 20
subnet 10.10.20.0 netmask 255.255.255.0 {
	option subnet-mask 255.255.255.0;
 	option routers 10.10.20.1;
  	option domain-name-servers 10.10.10.10;
  	default-lease-time 7200;
  	max-lease-time 86400;
 	pool {
   		failover peer "failover-dhcp";
    	range 10.10.20.100 10.10.20.254;
	}
}
```

Резервный сервер:

```shell
# DHCP Backup
authoritative;
failover peer "failover-dhcp" {
  secondary;
  address 10.10.10.31;
  port 520;
  peer address 10.10.10.30;
  peer port 519;
  max-response-delay 60;
  max-unacked-updates 10;
  load balance max seconds 3;
}

# VLAN 10
subnet 10.10.10.0 netmask 255.255.255.0 {
	option subnet-mask 255.255.255.0;
  	option routers 10.10.10.1;
 	option domain-name-servers 10.10.10.10;
  	default-lease-time 7200;
  	max-lease-time 86400;
  	pool {
  		failover peer "failover-dhcp";
    	range 10.10.10.100 10.10.10.254;
	}
}

# VLAN 20
subnet 10.10.20.0 netmask 255.255.255.0 {
	option subnet-mask 255.255.255.0;
 	option routers 10.10.20.1;
  	option domain-name-servers 10.10.10.10;
  	default-lease-time 7200;
  	max-lease-time 86400;
 	pool {
   		failover peer "failover-dhcp";
    	range 10.10.20.100 10.10.20.254;
	}
}
```

Проверяем через` systemctl status isc-dhcp-server`, что сервис на обоих серверах успешно запущен и работает:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2022-11/scaled-1680-/Pb6image.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2022-11/Pb6image.png)

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2022-11/scaled-1680-/Fg6image.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2022-11/Fg6image.png)

На этом конфигурация isc-dhcp-server закончена. Можно переходить к настройке dhcp-релея для переадресации запросов на наш DHCP-сервер.

##### **Настройка DHCP-relay на маршрутизаторе**

Устанавливаем пакет isc-dhcp-relay:

```shell
apt install -y isc-dhcp-relay
```

Визард можно просто протыкать. Далее идем в `/etc/default/isc-dhcp-relay` и добавляем серверы DHCP и саб-интерфейсы:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2022-11/scaled-1680-/qcEimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2022-11/qcEimage.png)

После этого перезапускаем службу dhcp-relay командой `systemctl restart isc-dhcp-relay` и смотрим её статус `systemctl status isc-dhcp-relay`:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2022-11/scaled-1680-/6znimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2022-11/6znimage.png)

На этом настройка dhcp-relay закончена. Переходим к тестированию и проверки.

##### **Проверка работоспособности DHCP и тестирование отказоустойчивости**

На клиенте командой ip a проверяем, что на интерфейсах нет никаких ip-адресов.

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2022-11/scaled-1680-/sLDimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2022-11/sLDimage.png)

Если ip-адрес на интерфейсе есть, то идём в конфигурационный файл `/etc/network/interfaces` и убеждаемся, что интерфейс установлен на получение адреса по dhcp:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2022-11/scaled-1680-/a86image.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2022-11/a86image.png)

Если нет, то пишем как на скриншоте и сохраняем файл, после чего перезапускаем службу networking командой s`ystemctl restart networking` и пишем `ip -a`:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2022-11/scaled-1680-/RKXimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2022-11/RKXimage.png)

Как видим хост получил ip-адрес из нужной подсети. Выключаем по питанию один из серверов и смотрим, что происходит со службой на оставшемся сервере командой `systemctl status isc-dhcp-server`:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2022-11/scaled-1680-/R18image.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2022-11/R18image.png)

На скриншоте видно, что служба dhcp на втором сервере работает и что произошло отключение другого dhcp-сервера. Возвращаемся на клиент и перезапускаем службу networking и смотрим получает ли хост ip-адрес:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2022-11/scaled-1680-/MEIimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2022-11/MEIimage.png)

Как видим - всё ок.