# zVirt Standalone

#### **Установка Standalone менеджера управления и хостов виртуализации**

Добавляем в файл /etc/hosts информацию по хостам в соответствии с заданием и топологией:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/LFRimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/LFRimage.png)

Переименовываем каждый хост в соответствии с заданием и топологией:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/C40image.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/C40image.png)

Конфигурируем только машину для менеджера управления:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/IhFimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/IhFimage.png)

На самих нодах прописываем <span lang="EN-US" style="mso-ansi-language: EN-US;">hosts</span>, задаём хостнеймы и меняем <span lang="EN-US" style="mso-ansi-language: EN-US;">ip</span>-адресацию.

Далее устанавливаем <span lang="EN-US" style="mso-ansi-language: EN-US;">zvirt</span>-<span lang="EN-US" style="mso-ansi-language: EN-US;">standalone</span>:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/px2image.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/px2image.png)

<span style="mso-fareast-language: RU; mso-no-proof: yes;"></span>

Далее приступаем к установке самого <span lang="EN-US" style="mso-ansi-language: EN-US;">Engine</span> – `<span lang="EN-US" style="mso-ansi-language: EN-US;">engine</span>-<span lang="EN-US" style="mso-ansi-language: EN-US;">setup</span>`:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/Mcbimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/Mcbimage.png)<span style="mso-fareast-language: RU; mso-no-proof: yes;"></span>

Везде оставляем всё по умолчанию:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/zu2image.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/zu2image.png)<span style="mso-fareast-language: RU; mso-no-proof: yes;"></span>

После установки заходим с любой машины в наш менеджер по адресу <span lang="EN-US" style="mso-ansi-language: EN-US;">[https<span lang="RU" style="mso-ansi-language: RU;">://</span>zmanager<span lang="RU" style="mso-ansi-language: RU;">.</span>ds<span lang="RU" style="mso-ansi-language: RU;">23.</span>local](https://zmanager.ds23.local) (FQDN вашей машины)</span> и скачиваем корневой сертификат:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/Juuimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/Juuimage.png)<span style="mso-fareast-language: RU; mso-no-proof: yes;"></span>

Далее нужно установить скачанный сертификат в браузер <span lang="EN-US" style="mso-ansi-language: EN-US;">FireFox</span>. Если делать для одной машины, то идём в настройки в раздел Приватность и защита, листаем вниз до сертификатов. Выбираем там просмотр сертификатов и импортируем наш серт (если вдруг в папке с загрузками пусто, значит сертификат скачался без расширения, добавляем просто ему расширение .<span lang="EN-US" style="mso-ansi-language: EN-US;">cer</span>):

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/i2limage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/i2limage.png)<span style="mso-fareast-language: RU; mso-no-proof: yes;"></span>

Перезапускаем браузер и снова открываем менеджер управления, ошибок по сертификату возникнуть не должно. Открываем далее портал администрирования:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/dUoimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/dUoimage.png)<span style="mso-fareast-language: RU; mso-no-proof: yes;"></span>

Логин **<span lang="EN-US" style="mso-ansi-language: EN-US;">admin</span>**, пароль который указали при установке менеджера (в моем случае P@ssw0rd):

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/daFimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/daFimage.png)<span style="mso-fareast-language: RU; mso-no-proof: yes;"></span>

Вот так выглядит главная страница с дашбордом. Как видим, хостов виртуализации у нас ни одного нет:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/cYwimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/cYwimage.png)<span style="mso-fareast-language: RU; mso-no-proof: yes;"></span>

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/zx6image.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/zx6image.png)

Идём в меню **Ресурсы -&gt; Хосты**:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/GI4image.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/GI4image.png)

Добавляем новый хост:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/hRJimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/hRJimage.png)

Кластер остается <span lang="EN-US" style="mso-ansi-language: EN-US;">Default</span> так как он один, задаем имя хосту, указываем его <span lang="EN-US" style="mso-ansi-language: EN-US;">fqdn</span>/<span lang="EN-US" style="mso-ansi-language: EN-US;">ip</span>, говорим, что нужно включиться и перезагрузиться при установке и задаем пароль:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/8Opimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/8Opimage.png)

Больше ничего настраивать не нужно, нажимаем **Ок** внизу.

Вылезет сообщение о том, что не настроено управление питанием, просто жмём **Ок** игнорируя его:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/HoYimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/HoYimage.png)

После этого появится запись с хостом и будет написано, что идёт инсталляция. Через некоторое время хост уйдет в перезагрузку – это нормально. Установка занимает минут 10 (может быть чуть больше):

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/P6aimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/P6aimage.png)<span style="mso-fareast-language: RU; mso-no-proof: yes;"></span>

Добавляем аналогично второй хост виртуализации. По итогу должны быть вот такие статусы и хосты должны быть в **<span lang="EN-US" style="mso-ansi-language: EN-US;">Up</span>**:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/vduimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/vduimage.png)<span style="mso-fareast-language: RU; mso-no-proof: yes;"></span>

Статус задач отображается вот здесь:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/peRimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/peRimage.png)<span style="mso-fareast-language: RU; mso-no-proof: yes;"></span>

#### **Добавление NFS и ISCSI-хранилищ**

Далее необходимо подключить <span lang="EN-US" style="mso-ansi-language: EN-US;">NFS</span>-хранилище и <span lang="EN-US" style="mso-ansi-language: EN-US;">ISCSI</span>-хранилище.

Сначала нужно создать и настроить эти хранилища. Идём на сервер, где будет файловое хранилище.

Добавляем два диска (они могут быть уже добавлены) размером по 70 Гб. Смотрим, что в <span lang="EN-US" style="mso-ansi-language: EN-US;">lsblk</span> диски нужного объема добавлены (если их нет, то ВМ надо перезагрузить после добавления в настройках ВМ). Далее заходим в утилиту <span lang="EN-US" style="mso-ansi-language: EN-US;">fdisk</span> в конкретный диск <span lang="EN-US" style="mso-ansi-language: EN-US;">fdisk</span> /<span lang="EN-US" style="mso-ansi-language: EN-US;">dev</span>/<span lang="EN-US" style="mso-ansi-language: EN-US;">sdb</span>:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/v1Ximage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/v1Ximage.png)

<span style="mso-fareast-language: RU; mso-no-proof: yes;"></span>

Создаем раздел введя <span lang="EN-US" style="mso-ansi-language: EN-US;">n</span>, выбираем раздел основной и дальше всё по умолчанию. По итогу создастся раздел типа **<span lang="EN-US" style="mso-ansi-language: EN-US;">Linux</span>**, нам же необходимо поменять его на **<span lang="EN-US" style="mso-ansi-language: EN-US;">LVM</span>**:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/ZICimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/ZICimage.png)

Вводим команду `<span lang="EN-US" style="mso-ansi-language: EN-US;">t</span>` для перехода в наш созданный раздел и вводим `<span lang="EN-US" style="mso-ansi-language: EN-US;">L</span>` для вывода списка кодов:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/cYBimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/cYBimage.png)<span style="mso-fareast-language: RU; mso-no-proof: yes;"></span>

Необходимо ввести `8<span lang="EN-US" style="mso-ansi-language: EN-US;">e</span>` в консоль для выбора **<span lang="EN-US" style="mso-ansi-language: EN-US;">LVM</span>**, далее сохраняем изменения и выходим:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/wwCimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/wwCimage.png)<span style="mso-fareast-language: RU; mso-no-proof: yes;"></span>

Аналогично делаем для другого диска под <span lang="EN-US" style="mso-ansi-language: EN-US;">ISCSI</span>. После этого ещё раз введём `<span lang="EN-US" style="mso-ansi-language: EN-US;">lsblk</span>` и посмотрим, что у нас получилось:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/6U1image.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/6U1image.png)<span style="mso-fareast-language: RU; mso-no-proof: yes;"></span>

Для<span style="mso-ansi-language: EN-US;"> <span lang="EN-US">ISCSI </span></span>установим<span style="mso-ansi-language: EN-US;"> </span>пакет<span style="mso-ansi-language: EN-US;"> <span lang="EN-US">`apt install targetcli-fb`.</span></span>

Далее инициализируем наши разделы:

`<span lang="EN-US" style="mso-ansi-language: EN-US;">pvcreate</span> /<span lang="EN-US" style="mso-ansi-language: EN-US;">dev</span>/<span lang="EN-US" style="mso-ansi-language: EN-US;">sdb</span>1 /<span lang="EN-US" style="mso-ansi-language: EN-US;">dev</span>/<span lang="EN-US" style="mso-ansi-language: EN-US;">sdc1</span>` и командой <span lang="EN-US" style="mso-ansi-language: EN-US;">pvscan</span> проверяем, что всё ок:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/HeSimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/HeSimage.png)<span style="mso-fareast-language: RU; mso-no-proof: yes;"></span>

Далее создаем группу разделов `<span lang="EN-US" style="mso-ansi-language: EN-US;">vgcreate</span> <span lang="EN-US" style="mso-ansi-language: EN-US;">vgr</span>p1 /<span lang="EN-US" style="mso-ansi-language: EN-US;">dev</span>/<span lang="EN-US" style="mso-ansi-language: EN-US;">sdb</span>1 /<span lang="EN-US" style="mso-ansi-language: EN-US;">dev</span>/<span lang="EN-US" style="mso-ansi-language: EN-US;">sdc</span>1`:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/KtBimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/KtBimage.png)<span style="mso-fareast-language: RU; mso-no-proof: yes;"></span>

Смотрим свойства нашей созданной группы `vgs vgrp1`:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/k4cimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/k4cimage.png)

Создаем логический раздел со своим объемом:

```
lvcreate -L 120G -n lv1 vgrp1
```

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/81ximage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/81ximage.png)

Прикрутить файловую систему:

`mkfs.xfs -b size=1024 /dev/vgrp1/lv1`

Далее:

Войти в `cli` утилиты `targetcli`

Внутри `cli`:

```
/backstores/block create stg1 /dev/vgrp1/lv1
/iscsi create
ls
/iscsi/iqn.2003-01.org.linux-iscsi.astra1.x8664:sn.0a5c116f301c/tpg1/luns create /backstores/block/stg1
cd /iscsi/iqn.2003-01.org.linux-iscsi.astra1.x8664:sn.0a5c116f301c/tpg1
set attribute generate_node_acls=1
set attribute demo_mode_write_protect=0
/ saveconfig
exit
```

В zvirt `GUI`:

Нажмите Хранилище (`Storage`) → Домены (`Domains`). Нажмите Новый домен (`New Domain`). Задайте Имя (`Name`) для нового домена хранения. Выберите Центр данных (`Data Center`) в раскрывающемся списке. Выберите Данные (`Data`) в качестве Функция домена (`Domain Function`) и iSCSI в качестве Типа хранилища (`Storage Type`). Выберите активный хост в качестве Используемый хост (`Host`).

7.1. Нажмите Обнаружение целей (`Discover Targets`), чтобы задать параметры обнаружения таргетов. После того как таргеты обнаружены и вход в них выполнен, в окне Новый домен хранения (`New Domain`) будут автоматически отображаться таргеты с LUN, которые не используются средой.

7.2. В поле Адрес (`Address`) укажите FQDN или IP-адрес iSCSI-хоста.

7.3. В поле Порт (`Port`) укажите порт для соединения с хостом в процессе поиска таргетов. Значение по умолчанию = 3260.

7.4. Если для защиты хранилища используется CHAP, установите флажок Аутентификация пользователей (`User Authentication`). Введите Имя пользователя CHAP (`CHAP user name`) и Пароль CHAP (`CHAP password`).

7.5. Нажмите Обнаружение (`Discover`).

7.6. Выберите один или несколько таргетов из результатов поиска и нажмите arr-right для одного таргета или Войти везде (`Login All`) для нескольких таргетов.

Нажмите plus рядом с нужным таргетом. Развернется соответствующая запись с отображением всех неиспользуемых LUN, подключенных к таргету.

Поставьте флажок для каждого LUN, который используете для создания домена хранения.

[https://www.linuxtechi.com/how-to-create-lvm-partition-in-linux/](https://www.linuxtechi.com/how-to-create-lvm-partition-in-linux/) [https://wiki.astralinux.ru/brest/latest/hranilishcha-na-baze-fajlovoj-tehnologii-hraneniya-275950642.html](https://wiki.astralinux.ru/brest/latest/hranilishcha-na-baze-fajlovoj-tehnologii-hraneniya-275950642.html) [https://clck.ru/35nK4d](https://clck.ru/35nK4d)

Идём в раздел Хранилище -&gt; Домены:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/LUeimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/LUeimage.png)

<span style="mso-fareast-language: RU; mso-no-proof: yes;">  
</span>

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/uqHimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/uqHimage.png)

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/BGgimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/BGgimage.png)

<span style="mso-fareast-language: RU; mso-no-proof: yes;">  
</span>