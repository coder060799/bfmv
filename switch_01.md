# OpenVSwitch

#### **Настраиваем OpenVSwitch**

##### **Установка в Astra Linux**

```shell
apt install debian-archive-keyring # для возможности подключения старых debian репозиториев
echo "deb https://mirror.yandex.ru/debian stretch main contrib non-free" >> /etc/apt/sources.list # добавляем debian 9 репозиторий
apt update # обновляем список пакетов
apt install -y openvswitch-switch # Устанавливаем openvswitch
systemct enable openvswitch-switch # добавлявем в автозагрузку ovs
```

#### **Работаем с OpenVSwitch**

**Так как коммутация отдается openvswitch - на гипервизоре создаем отдельную порт-группу под каждый адаптер свитча (портгруппы = провода)**

##### **Работаем с интерфейсами**

```shell
ovs-vsctl add-br BR1 # добавляем в ovs новый бридж BR1
ovs-vsctl add-port BR1 eth1 # добавлявем в бридж интерфейс eth1. остальные добавляются по аналогии
ovs-vsctl set port BR1 eth1 tag = 10 # добавляем на интерфейс тегирование (порт во vlan)
ovs-vsctl set port BR1 eth1 trunks=10,20,30 # делаем порт транковым, разрешенные вланы через запятую
```

##### **Настройка STP/RSTP**

Для включения stp/rstp необходимо выполнить одну из следующих команд:

```shell
ovs-vsctl set bridge BR1 stp_enable=true # включаем stp
ovs-vsctl set bridge BR1 rstp_enable=true # включаем rstp
```

Отключается заменой true на false.

В Astra Linux OpenVSwitch слишком старый, поэтому команды `ovs-appctl stp/show` или `ovs-appctl rstp/show` нет.

Настройка LACP/Статическое аггрегирование

```shell
ovs-appctl bond/show -- проверяем, что всё работает
```

Транки настраиваются как на портах, т.е. `ovs-vsctl set port BR1 bond0 trunks=10,20,30`

##### **Роутер на палочке**

На RTR сначала делаем systemctl mask NetworkManager и перезагружаемся

Потом идем в `/etc/network/interfaces`

Пример конфига ниже:

```shell
auto eth0
iface eth0 inet manual

# VLAN 10
auto eth0.10
iface eth0.10 inet static
	address 10.10.10.1/24
    
 auto eth1
 iface eth1 inet manual
 
 # VLAN 20
 auto eth1.20
 iface eth1.20 inet static
 	address 10.10.20.1/24
```

После этого делаем `systemctl restart networking`.

##### **Команды для удаления конфигурации**

```shell
ovs-vsctl del-port eth1 # удалить порт
ovs-vsctl del-port bond0 # удалить бонд
ovs-vsctl del-br BR # удалить бридж
```