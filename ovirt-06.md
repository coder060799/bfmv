# Zvirt (не проверено)

#### **Полезные материалы**

К изучению следующие статьи:

[https://kb.pvhostvm.ru/hostvm/installation-guide/ustanovka-hostvm-4.3-4.4/pered-ustanovkoi-hostvm-manager/podgotovka-nfs-share](https://kb.pvhostvm.ru/hostvm/installation-guide/ustanovka-hostvm-4.3-4.4/pered-ustanovkoi-hostvm-manager/podgotovka-nfs-share)

[https://kb.pvhostvm.ru/hostvm/installation-guide/ustanovka-hostvm-4.3-4.4/ustanovka-hostvm-manager-4.4.8-gui/ustanovka-hostvm-manager-na-nfs](https://kb.pvhostvm.ru/hostvm/installation-guide/ustanovka-hostvm-4.3-4.4/ustanovka-hostvm-manager-4.4.8-gui/ustanovka-hostvm-manager-na-nfs)

[https://blog.it-kb.ru/2016/09/10/install-ovirt-4-0-part-1-create-two-node-hosted-engine-cluster-with-shared-fc-san-storage/](https://blog.it-kb.ru/2016/09/10/install-ovirt-4-0-part-1-create-two-node-hosted-engine-cluster-with-shared-fc-san-storage/)

#### **Подготовка NFS-хранилища**

Нужно развернуть NFS-шару. Можно на том же хосте zvirt, можно на отдельном. Если ставим на от дельном сервере, то ставим пакет с NFS и выполняем следующие команды:

```shell
# Создать служебных пользователей и группы:
groupadd sanlock -g 179
groupadd kvm -g 36
useradd sanlock -u 179 -g 179 -G kvm
useradd vdsm -u 36 -g 36 -G sanlock
```

Хранилище должно быть не меньше 61 Гб. Можно сразу создать ВМ с нужным объемом свободного пространства, либо

 отдельно добавить диск нужного объема. Пойдем по второму пути и добавим диск на 70 Гб. После добавления перезагружаем нашу ВМ и проверяем командой lsblk, что диск появился:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/O7Vimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/O7Vimage.png)

В моём случае устройство называется sdc. Переходим в утилиту fdisk ( fdisk /dev/sdc ) с выбором нашего устройства:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/d22image.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/d22image.png)

Вводим n для создания раздела, вводим p для выбора раздела primary, далее всё по умолчанию:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/pNsimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/pNsimage.png)

Записываем изменения командой w:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/EXPimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/EXPimage.png)

Смотри вывод lsblk и видим, что теперь появился новый раздел sdc1:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/i71image.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/i71image.png)

Теперь на разделе необходимо создать файловую систему с помощью утилиты mkfs, указав после точки тип файловой системы:

```shell
mkfs.ext4 /dev/sdс1
```

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/YG3image.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/YG3image.png)

<p class="callout info">Примечание: расширенный раздел не может быть отформатирован с файловыми системами, такими как ext3, FAT или NTFS, и не может непосредственно содержать данные.</p>

Далее необходимо создать точку монтирования для раздела:

```shell
mkdir /mnt/storage2
```

Выдаем права на созданный каталог:

```shell
chown 36:36 /mnt/storage2
chmod 0775 /mnt/storage2
```

Для автоматического монтирования разделов после перезагрузки сервера внесите изменения в файл /etc/fstab:

```
/dev/sdc1      /mnt/storage2   ext4   defaults   0 0
```

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/GVjimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/GVjimage.png)

Монтируем раздел через `mount -a`

Опубликовать каталог, прописав его в конфигурационном файле `NFS-сервера`:

```
echo "/mnt/storage2 *(rw,anonuid=36,anongid=36)" >> /etc/exports
```

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/CD9image.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/CD9image.png)

<p class="callout info">В Astra/Debian необходимо обязательно в /etc/exports добавить опцию no\_subtree\_check после anongid=36</p>

Экспортируем все директории командой `exportfs -a`, при выполнении команды ошибок возникнуть не должно.

Создать правила межсетевого экрана для обеспечения доступности хранилища для других хостов:

```
firewall-cmd --permanent --add-service=nfs
firewall-cmd --permanent --add-service=mountd
firewall-cmd --permanent --add-service=rpc-bind
firewall-cmd --reload
```

#### Установка хоста zVirt

Создаем виртуалку в любом гипервизоре и запускаем с образа zvirt.iso. Установка один в один как дистрибутив CentOS:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/VQrimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/VQrimage.png)

Язык по умолчанию оставляем английский:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/D89image.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/D89image.png)

Нужно выполнить предварительные настройки перед установкой, идём поэтапно по каждому пункту и обязательно заходим в настройки сетевого адаптера:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/gSiimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/gSiimage.png)

Не забываем включить сетевой адаптер, можно сразу задать имя хосту:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/sy6image.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/sy6image.png)

<p class="callout info">IP лучше задать сразу статический.</p>

Раскладку оставляем одну:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/6Hcimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/6Hcimage.png)

Задаем какой-нибудь пароль пользователю root, я выбрал P@ssw0rd (он ненадежный, поэтому кликаем по Done 2 раза):

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/IkXimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/IkXimage.png)

У меня тут уже был занят диск. Я его почистил. У вас будет пустой диск.

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/WYAimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/WYAimage.png)

Пошла установка:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/mYrimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/mYrimage.png)

Перезагружаем систему вручную:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/Bk0image.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/Bk0image.png)

Далее необходимо залогиниться на ВМ и выполнить следующие действия:

```shell
# включить репозиторий
dnf config-manager --enable centos-zvirt-main

# указать данные для доступа к репозиторию
zvirt-credentials -u USERNAME -p PASSWORD 

# cледующие команды должны отработать без ошибок, напротив репозитория должно отображаться enabled:
dnf clean all
dnf repolist all

dnf update

dnf install -y zvirt-hosted-engine
```

<p class="callout info">**Обязательно настроить использование DNS-сервера, чтобы хост мог разрешить имена.   
Также обязательно переименовать хост по FQDN.** </p>

Далее в файле /etc/hosts лучше прописать dns-записи:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/5Xeimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/5Xeimage.png)

#### **Установка Hosted Engine** 

Предварительно на ESXI включить виртуализацию в разделе CPU

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/DWQimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/DWQimage.png)

Переходим по адресу https://&lt;ip&gt;:9090, идём в раздел zVirt и запускаем установку Hosted Engine (служба управления):

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/image.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/image.png)

Запустится мастер установки:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/aGiimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/aGiimage.png)

Заполняем следующие поля по аналогии с моими:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/OqWimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/OqWimage.png)

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/bN5image.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/bN5image.png)

Заполняем дальше:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/dOBimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/dOBimage.png)

И приступаем к установке:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/XaJimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/XaJimage.png)

Процесс установки занимает довольно много времени.

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/Z5iimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/Z5iimage.png)

После подготовки нужно будет добавить хранилище:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/hgQimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/hgQimage.png)

Используем ранее созданную NFS-шару (скрин старый, название каталога должно быть как создали в пунктах с NFS-шарой):

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/B6zimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/B6zimage.png)

Запускаем установку:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/uHdimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/uHdimage.png)

Спустя некоторое время установка завершится без ошибок (скрещиваем пальцы):

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/oBkimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/oBkimage.png)

##### **Исправление хоста после неудачного развертывания менеджера управления**

Если по каким-либо причинам развертывание менеджера управления закончилось ошибкой, хост необходимо очистить:

1. Для режима **Hosted Engine** с помощью команды:
    
    ```
    ovirt-hosted-engine-cleanup
    
    ```
2. Для режима **Standalone** с помощью команды:
    
    ```
    engine-cleanup
    ```

Ну и вот так выглядит теперь наш веб-интерфейс:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/GfTimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/GfTimage.png)

Далее переходим в веб-интерфейс управления. https://&lt;fqdn&gt;:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/PZqimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/PZqimage.png)

Если зайдем по ip-адресу, то словим вот такую веб-страницу:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/y6iimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/y6iimage.png)

Идём в портал администрирования и вводим учетную запись:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/b4Mimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/b4Mimage.png)

Учетная запись admin с паролем, который указали ранее для доступа в веб-интерфейс:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/4HLimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/4HLimage.png)

Вот так это выглядит:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/c1Bimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/c1Bimage.png)

#### **Загрузка ISO-образов**

Идём в Хранилище -&gt; Диски и нажимаем Загрузить в правом верхнем углу, выбираем Начать:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/u6Yimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/u6Yimage.png)

Делаем Тест соединения:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/2KWimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/2KWimage.png)

Выбираем диск, корректируем информацию и нажимаем Ок:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/g3ximage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/g3ximage.png)

После этого начнется процесс загрузки образа в zVirt:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/Y86image.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/Y86image.png)

##### **Ошибка загрузки ISO-образа**

Если в процессе загрузки появится вот такая ошибка:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/ZPBimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/ZPBimage.png)

В таком случае нужно увеличить таймаут передачи образов. Подключаемся по SSH на наш менеджер (в моем случае 88.247) и вводим следующие команды:

```shell
engine-config -s TransferImageClientInactivityTimeoutInSeconds=6000
systemctl restart ovirt-engine
```

После этого возобновляем загрузку образа. По итогу всё успешно загрузилось:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/EIPimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/EIPimage.png)

#### **Создание виртуальной машины**

Идём в Ресурсы -&gt; Виртуальные машины и нажимаем Создать:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/qm1image.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/qm1image.png)

Выбираем версию операционной системы, задаём имя для ВМ и создаём виртуальный диск:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/juBimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/juBimage.png)

В свойствах диска указываем размер и указываем, что он должен быть загрузочным:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/RW1image.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/RW1image.png)

Далее раскрываем расширенные свойства нашей создаваемой ВМ:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/Qs3image.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/Qs3image.png)

Тут можно задать различные параметры виртуальной машины:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/g3Bimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/g3Bimage.png)

Самое главное - включить CD-привод и выбрать нужный образ:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/3Lcimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/3Lcimage.png)

После этого визард закроется, запись об машине появится в списке ВМ, а в правом нижнем углу появится уведомление:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/pt7image.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/pt7image.png)

Список задач можно посмотреть вот тут:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/Z1Iimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/Z1Iimage.png)

Выделяем и запускаем нашу созданную ВМ:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/NRWimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/NRWimage.png)

Далее жмём на Консоль, скачается и запустится Spice:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/jBCimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/jBCimage.png)

<p class="callout info">Наверное можно добавить диск как устройство и выставить приоритет загрузки с него</p>

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/6DIimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/6DIimage.png)

<p class="callout info">Для выхода из Spice использовать сочетание клавиш Ctrl + F12</p>

Дальше всё как с обычными ВМ:

[![image.png](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/scaled-1680-/Z8fimage.png)](https://atomskills.cubeee.ru/uploads/images/gallery/2023-09/Z8fimage.png)