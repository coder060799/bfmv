# Домен на FreeIPA

##### **Подготовка сервера**

Перед установкой и настройкой Freeipa необходимо выполнить подготовку сервера. Нужно настроить файл hosts, имя сервера, отключить и замаскировать службу NetworkManager, создать и настроить файл /etc/resolv.conf

Редактируем hostname:

`hostnamectl set-hostname dc.ht22.local`

Редактируем файл hosts в такой формат:

&lt;ip сервера&gt; &lt;FQDN&gt; &lt;shortname&gt;

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2022-10/scaled-1680-/HZmimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2022-10/HZmimage.png)

Также необходимо замаскировать службу NetworkManager и отключить её, иначе будут конфликты со службой networking:

`systemctl mask NetworkManager`

`systemctl disable NetworkManager`

После отключения службы создаем файл resolv.conf в /etc и пишем следующее:

```shell
search ht22.local
domain ht22.local
```

После этого перезагружаем сервер. Подготовка к установке и настройке FreeIPA успешно выполнена.

##### **Установка и настройка astra-freeipa-server**

Установка FreeIPA занимает несколько минут. В процессе нужно несколько раз подтвердить действия. Установка выполняется командой:

`apt install -y astra-freeipa-server`

После установки необходимо повысить сервер до контроллера домена. Сделать это можно, введя команду `astra-freeipa-server install` без параметров. Все нужные параметры он подберет сам. Либо можно воспользоваться командой `astra-freeipa-server install -d ht22.local -o -i 10.10.10.10 `(-d - имя домена, -o - изолированная сеть, -i - ip-адрес интерфейса).

После этого проверяем конфигурацию и вводим пароль администратора [P@ssw0rd](mailto:P@ssw0rd). После 5 минут ожиданий выйдет сообщений об успешной настройке FreeIPA.

##### **Вход в веб-интерфейс и работа с FreeIPA**

Для входа в веб-интерфейс управления FreeIPA нужно открыть Firefox и ввести [https://dc.ht22.local](https://dc.ht22.local). Вылезет окно о том, что нет доверия сертификата. Это нормально, так как Firefox по умолчанию не умеет забирать сертификаты из корневого хранилища.

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2022-10/scaled-1680-/goCimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2022-10/goCimage.png)

##### **Создание пользователей**  


У FreeIPA есть особенность при создании учетных записей пользователей через веб-интерфейс. Срок действия пароля заканчивается очень быстро. Скорее всего сделано с целью безопасности, чтобы пользователь сразу же менял свой пароль при входе в домен (команда login). Чтобы не логиниться под каждым юзером для смены пароля, можно создавать учетки с помощью терминала.

Для этого вводим следующую команду:

```shell
ipa user-add anivanov--first "Anton" --last "Ivanov" --cn "Anton Ivanov" --displayname "Anton Ivanov" --password-expiration=20221130000000Z --password
```

Будет предложено ввести пароль в интерактивном режиме с подтверждением. Вводим стандартный [P@ssw0rd](mailto:P@ssw0rd). В итоге получим такую картину:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2022-10/scaled-1680-/7Zfimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2022-10/7Zfimage.png)

После этого в веб-интерфейсе можно посмотреть свойства учетной записи, где будет указано, когда истекает пароль УЗ:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2022-10/scaled-1680-/tukimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2022-10/tukimage.png)

##### **Список пользователей FreeIPA**

Можно вывести список пользовательских аккаунтов FreeIPA с помощью команды `ipa-user-find`.

Для вывода всех имеющихся аккаунтов можно использовать простую команду:

```
ipa user-find --all
```

Для вывода определенного аккаунта:

```shell
ipa user-find USERNAME
```

Пример:

```
ipa user-find jdoe
```

LДопонительно можно посмотреть в справке**`ipa user-find --help`**.

##### **Редактирование учетных записей FreeIPA**

Для изменения атрибутов пользователя необходимо использовать команду`ipa`` user-mod`<span class="ezoic-ad ezoic-at-0 leader-4 leader-4110 adtester-container adtester-container-110" data-ez-name="kifarunix_com-leader-4"><span class="ezoic-ad" id="bkmrk--3"></span></span>

Например, можно вот так изменить параметр shell для пользователя:

```shell
ipa user-mod USERNAME --shell=/bin/bash
```

**USERNAME** это логин пользователя.

Для просмотра остальных атрибутов необходимо ввести команду**`ipa user-mod --help`**.

Для удаления пользователя можно использовать команду **`ipa user-del`**

```shell
ipa user-del USERNAME
```