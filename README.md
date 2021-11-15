### Компонент типографа для Yii2

Пакет позволяет встроить типограф в Yii2-фреймворк. Пакет состоит из поведения и компонента типографа. 

### Установка
<hr>

Чтобы установить пакет, нужно в composer.json добавить следующие строки:

```php
"require": {
    "chulakov/yii2-typograph": "^1.0.0",
}
```

Или набрать команду:

```php
composer require chulakov/yii2-typograph
```

### Способ подключения компонента
<hr>

Подключение компонента реализовано через внедрение зависимостей<br>
В реализации интерфейса BootstrapInterface прописывается код: 

```php
\Yii::$container->setSingleton('Chulakov\PhpTypograph\TypografInterface', function() use ($app) {
    return $app->get('typograph');
});
```

В файле common/config/main добавляется компонент. Компонент должен реализовывать интерфейс TypografInterface из пакета <a href="https://github.com/OlegChulakovStudio/ch-php-typograph">chulakov/ch-php-typograph</a>:

```php
return [
	'components' => [
		...
		'typograph' => [
	    	'class' => 'Chulakov\Typograph\TypographComponent',
	    ],
	]
];
```

В компоненте указан класс типографа по умолчанию <a href="https://github.com/OlegChulakovStudio/ch-php-typograph/blob/main/src/TypographFacade.php">TypographFacade</a>

Есть возможность переопределить типограф внутри компонента. Типограф должен быть реализацией интерфейса TypografInterface из пакета <a href="https://github.com/OlegChulakovStudio/ch-php-typograph">chulakov/ch-php-typograph</a>, как и сам компонент


```php
return [
	'components' => [
		...
		'typograph' => [
		    'class' => 'Chulakov\Typograph\TypographComponent',
		    'typographClass' => 'Chulakov\Typograph\TypographFacade',
		],
	]
];
```

Есть возможность переопределить правила типографа внутри компонента. Для этого в свойства компонента additionalRulesPath и changedRulesPath кладутся 
пути файлов с новыми правилами типографа и/или с изменениями старых правил. Приведены примерные пути файлов.

Замечание 1: новые правила - правила, которые необходимо добавить к старым<br>
Замечание 2: <a href="https://github.com/OlegChulakovStudio/ch-php-typograph/blob/main/README.md">пример задания конфигурации правил или их изменений (пункт 3-4)</a>
 

```php
return [
	'components' => [
		...
		'typograph' => [
			'class' => 'Chulakov\Typograph\TypographComponent',
			'additionalRulesPath' => '@common/components/typograph/config/additionalRules.php',
			'changedRulesPath' => '@common/components/typograph/config/changedRules.php',
		],
	]
];
```

В компоненте TypographComponent реализована функция process, которая типографирует текст:

```php
$typographComponent->process('до н. э.');
```

### Способ подключения поведения
<hr>

Поведение можно прикреплять к форме и указывать поля, значение которых необходимо типографировть

```php
public function behaviors()
{
	return [
		[
		    'class' => TypographBehaviour::class,
		    'attributes' => ['title']
		],
	];
}
```

Если поле формы составное, например, если поле является MultipleInput объектом, то его следует задавать в виде массива:

```php
public function behaviors()
{
	return [
		[
			'class' => TypographerBehaviour::class,
			'attributes' => [
			    'items' => [
			        'properties' => ['title', 'description']
			    ]
			]
		],
	];
}
```