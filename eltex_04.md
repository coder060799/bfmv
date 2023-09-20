# Настройка Eltex

#### **Настраиваем vESR**

##### **Документация** 

<div id="bkmrk-https%3A%2F%2Fdocs.eltex-c"><div>https://docs.eltex-co.ru/pages/viewpage.action?pageId=324534407</div></div><div id="bkmrk-default-login%2Fpasswo"><div>default login/password – admin/password</div></div><div id="bkmrk-%D0%A1%D1%80%D0%B0%D0%B7%D1%83-%D0%BF%D0%BE%D1%81%D0%BB%D0%B5-%D0%B2%D1%85%D0%BE%D0%B4%D0%B0-ve"><div>Сразу после входа vESR просит сменить пароль.</div></div>#####   


<div id="bkmrk-vesr-%D1%80%D0%B0%D0%B1%D0%BE%D1%82%D0%B0%D0%B5%D1%82-%D0%BF%D0%BE-%D1%81%D0%B8%D1%81"><div>VESR работает по системе контроля версий, все изменения нужно их подтверждать </div><div><div>  
</div></div></div>```shell
#Чтобы изменение вступило в силу нужно ввести комнду 
commit
#После этого изменения вступают в силу и начинается отсчёт 600 секунд на подтвереждение изменений. Если изменения не подтвердить конфигурация откатится до последней сохранённой.
#Для подтверждения изменений ввести команду:
confirm
```

<div id="bkmrk-%D0%94%D0%BB%D1%8F-%D0%BD%D0%B0%D0%B7%D0%BD%D0%B0%D1%87%D0%B5%D0%BD%D0%B8%D1%8F-%D0%B8%D0%BC%D0%B5%D0%BD%D0%B8"><div>Для назначения имени устройства используются следующие команды:</div><div></div></div>```shell
esr# configure
esr(config)# hostname <new-name>
```

<div id="bkmrk-%D0%9F%D1%80%D0%B8%D0%BC%D0%B5%D1%80-%D1%81%D0%BE%D0%B7%D0%B4%D0%B0%D0%BD%D0%B8%D1%8F-%D0%BF%D0%BE%D0%BB%D1%8C"><div>Пример создания пользовтелей и назначения привелегий:</div></div>```shell
esr# configure
esr(config)# username fedor
esr(config-user)# password 12345678
esr(config-user)# privilege 15
esr(config-user)# exit
esr(config)# username ivan
esr(config-user)# password password
esr(config-user)# privilege 1
esr(config-user)# exit
```

<div id="bkmrk-%D0%9D%D0%B0-vesr-%D0%B8%D1%81%D0%BF%D0%BE%D0%BB%D1%8C%D0%B7%D1%83%D1%8E%D1%82%D1%81%D1%8F"><div>На vESR используются security-zone, чем-то напомниющая Huawei USG. Пример создания зон и правил any any между ними:</div></div>```shell
security zone trust
exit
security zone untrust
exit

security zone-pair trust untrust
  rule 1
    action permit
    enable
  exit
exit
security zone-pair trust trust
  rule 1 
    action permit
    enable
  exit
exit

security zone-pair untrust trust
  rule 1
    action permit
    enable
  exit
exit
```

<div id="bkmrk-%D0%9F%D0%BE-%D1%83%D0%BC%D0%BE%D0%BB%D1%87%D0%B0%D0%BD%D0%B8%D1%8E-%D0%BD%D0%B0-vesr"><div>По умолчанию на vESR для доступа к маршрутизаотру разрешено подключение по ssh,telnet из зоны trusted.</div><div>Пример команд для разрешения пользователям из зоны «untrusted» с IP-адресами 132.16.0.5-132.16.0.10 подключаться к маршрутизатору с IP-адресом 40.13.1.22 по протоколу SSH:</div></div>```shell
#создаём объектные группы:
esr# configure
esr(config)# object-group network clients
esr(config-addr-set)# ip address-range 132.16.0.5-132.16.0.10
esr(config-addr-set)# exit
esr(config)# object-group network gateway
esr(config-addr-set)# ip address-range 40.13.1.22
esr(config-addr-set)# exit
esr(config)# object-group service ssh
esr(config-port-set)# port-range 22
esr(config-port-set)# exit

#Делаем правило из зоны uutrust к машрутизатору. self – зона, в которой находится интерфейс управления маршрутизатором.
esr(config)# security zone-pair untrusted self
esr(config-zone-pair)# rule 10
esr(config-zone-rule)# action permit
esr(config-zone-rule)# match protocol tcp
esr(config-zone-rule)# match source-address clients
esr(config-zone-rule)# match destination-address gateway
esr(config-zone-rule)# match destination-port ssh
esr(config-zone-rule)# enable
esr(config-zone-rule)# exit
esr(config-zone-pair)# exit
```

<div id="bkmrk-%D0%A1%D0%BE%D0%B7%D0%B4%D0%B0%D1%91%D0%BC-%D0%BD%D1%83%D0%B6%D0%BD%D1%8B%D0%B5-%D0%BD%D0%B0%D0%BC-v"><div>Создаём нужные нам VLAN</div></div>```shell
vlan 10
  force-up
exit
vlan 20
  force-up
exit

```

Настройка интерфейса + DHCP Relay

```shell
#На vESR не забыть глобально веключить DHCP-RELAY
esr(config)# ip dhcp-relay

#Пример создания саб-интерфейса c функцией DHCP-Relay
interface gigabitethernet 1/0/2.20
  security-zone trust
  ip address 10.1.2.1/24
  ip helper-address 10.1.1.2
exit
```

<div id="bkmrk-source-nat%2C-%D0%B2%D1%81%D1%91-%D1%87%D1%82%D0%BE-"><div>Source NAT, всё что идёт в интерфейс untrust натится в адрес интерфейса.</div></div>```shell
nat source
  ruleset INET
    to zone untrust
    rule 10
      action source-nat interface
      enable
    exit
  exit
exit
```


<div id="bkmrk-%D0%9D%D0%B0%D1%81%D1%82%D1%80%D0%BE%D0%B9%D0%BA%D0%B0-lacp-%D0%BD%D0%B0-ve"><div>Настройка LACP на vESR. Предварительно на интерфейсах отключить зону безопасности командой «no security-zone».Либо сбросить настройки.</div><div>https://docs.eltex-co.ru/pages/viewpage.action?pageId=324534410#id-Управлениеинтерфейсами-НастройкаLACP </div></div><div id="bkmrk-"></div>```shell


no interface gigabitethernet 1/0/1-2

esr(config)# interface port-channel 2
esr(config)# interface gigabitethernet 1/0/1-2
esr(config-if-gi)# channel-group 2 mode auto
```

<div id="bkmrk-%D0%9D%D0%B0%D1%81%D1%82%D1%80%D0%BE%D0%B9%D0%BA%D0%B0-%D0%B4%D0%B8%D0%BD%D0%B0%D0%BC%D0%B8%D1%87%D0%B5%D1%81%D0%BA"><div>Настройка динамической маршрутизации</div><div>https://docs.eltex-co.ru/pages/viewpage.action?pageId=324534452</div></div>OSPF vESR

```shell
router ospf 1
  area 1.1.1.1
    network 10.1.5.0/30
    network 10.1.10.0/24
    network 10.1.4.0/24
    enable
  exit
  enable
exit


interface gigabitethernet 1/0/1.5
  ip firewall disable
  ip address 10.1.5.2/30
  ip ospf instance 1
  ip ospf area 1.1.1.1
  ip ospf
exit
```

OSPF FRR

```shell
router ospf
 passive-interface default
 no passive-interface eth1.5
 network 10.1.5.0/30 area 1.1.1.1
 network 10.1.20.0/24 area 1.1.1.1
 default-information originate metric 1 metric-type 1
```