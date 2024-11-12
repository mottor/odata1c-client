```php
use \Dakword\OData1C\Client\ODataClient;

// клиент
$oData = new ODataClient($baseUri, 'login', 'password');

// дополнительные опциональные настройки клиента
$oData->setProxy('http://123.123.123.123:8080');
$oData->setTimeout(600);

// получить массив объектов метаданных,
// которые _включены_ в стандартный OData
$objects = $oData->getObjects();

// получить описание стандартного интерфейса OData в формате atom-xml
$metadata = $oData->getMetadata();
```

## Построение запроса

### query()
```php
// запрос к справочнику «Номенклатура»
$oData->query('Справочник_Номенклатура');
$oData->query('Catalog_Номенклатура');

// запрос к табличной части «Контактная информация» справочника «Склады»
$oData->query('Справочник', 'Склады_КонтактнаяИнформация');
$oData->query('Справочник', 'Склады', 'КонтактнаяИнформация');

// запрос к записям регистра сведений «Цены номенклатуры»
$oData->query(['Регистр сведений', 'ЦеныНоменклатуры', 'RecordType']);
```

### $select
```php
$oData->query('Справочник_Номенклатура')
    ->select(
        'Ref_Key, Parent_Key',
        'Description',
        'IsFolder'
    );

$oData->query('Справочник_Номенклатура')
    ->select('Ref_Key, Parent_Key, Presentation, IsFolder');
    
$oData->query('Справочник_Номенклатура')
    ->select(['Ref_Key', 'Parent_Key', 'Description', 'IsFolder']);
    
$oData->query('Справочник_Номенклатура')
    ->select('Ref_Key, Parent_Key, IsFolder')
    ->select('Description');

$oData->query('Документ_ЗаказКлиента')
    ->select(
        '*',
        ['Товары' => 'Номенклатура_Key, Характеристика_Key, Количество'],
    );
```

### $expand
```php
$oData->query('Документ_ЗаказКлиента')
    ->expand('Организация', 'Контрагент/*');

$oData->query('Справочник_ВидыЦен')
    ->expand('ВалютаЦены')
    ->select(
        'Ref_Key, Description',
        ['ВалютаЦены' => 'Ref_Key, Description, Code'],
    );
```

### $order
```php
$oData->query('Справочник_Номенклатура')
    ->orderBy('IsFolder', 'Description');

$oData->query('Документ_ЗаказКлиента')
    ->orderBy('Контрагент/ИНН')
    ->orderByDesc('Date');

$oData->query('Справочник_Номенклатура')
    ->orderBy([
        ['Description', 'desc'],
        'IsFolder',
    ]);
```

### $skip, $top
```php
// вернуть первые 50 результатов 
$oData->query('Справочник_Номенклатура')->top(50);

// исключить из результатов первые 10 записей 
$oData->query('Справочник_Номенклатура')->skip(10);

// исключить из результатов первые 20 записей и вернуть 50 записей 
$oData->query('Справочник_Номенклатура')->skip(20)->top(50);

// вернуть 3-ю сотню результатов 
$oData->query('Справочник_Номенклатура')->page(3);

// вернуть 3-ю тысячу результатов 
$oData->query('Справочник_Номенклатура')->page(3, 1000);
```

### count(), $inlinecount
```php
// количество записей в справочнике «Номенклатура»
$count = $oData->query('Справочник_Номенклатура')->count();

// количество записей в справочнике «Номенклатура» удовлетворяющих отбору
$count = $oData->query('Справочник_Номенклатура')->...filter...->count();

// получить 2-ю страницу результатов и добавить к ним общее количество результатов
$oData->query('Справочник_Номенклатура')->...filter...->page(2)->addCount();
```

### call()
```php
// выполнение функций

// "InformationRegister_КурсыВалют/SliceFirst()",
$oData->query('Регистр сведений', 'КурсыВалют')
    ->call('SliceFirst');

// "InformationRegister_КурсыВалют/SliceFirst(Period=datetime'2024-09-01T00:00:00')",
$oData->query('Регистр сведений', 'КурсыВалют')
    ->call('SliceFirst', [
        'Period' => "datetime'2024-09-01T00:00:00'",
    ]);
```

### $filter
```php
// where()
$oData->query('Документ_ЗаказКлиента')
    ->where('IsFolder', '=', true)
    ->where('IsFolder', true)
    ->where('ХозяйственнаяОперация', '<>', 'РеализацияКлиенту')
    ->where('СуммаДокумента', '<=', 1500);
    ->where('СуммаДокумента div 10', '<', 300);
    ->where([
        ['IsFolder', '=', true],
        ['IsFolder', true],
        'IsFolder = true',
    ])
    ->where('Имя eq \'Молоко\' and Цена lt 2500')

// whereTrue(), whereFalse(), whereNull(), whereNotNull()
$oData->query('Справочник_Номенклатура')
    ->whereTrue('IsFolder')
    ->whereFalse('DeletionMark')
    ->whereNull('Артикул')
    ->whereNotNull('Описание')

// whereGUID()
$oData->query('Документ', 'ЗаказКлиента', 'Товары')
    ->whereGUID('ВидНоменклатуры_Key', '7382ee2f-cd57-11e4-869d-0050568b35ac');
    ->whereGUID('Parent_Key', '00000000-0000-0000-0000-000000000000');

// orWhere()
$oData->query('Документ_ЗаказКлиента')
    ->where('СуммаДокумента', '<>', 100)
    ->orWhere('СуммаДокумента', '>=', 2000);

// группировка условий
// Document_ЗаказКлиента?$filter=(ФормаОплаты eq 'ПлатежнаяКарта' or ФормаОплаты eq 'Безналичная') and СуммаДокумента gt 2000
$oData->query('Документ_ЗаказКлиента')
    ->where(fn($query) => $query
        ->where('ФормаОплаты', 'ПлатежнаяКарта') 
        ->orWhere('ФормаОплаты', 'Безналичная') 
    )
    ->where('СуммаДокумента', '>', 2000);
```

## Выполнение запроса

### получение набора сущностей

```php
// get()
$items = $oData->query('Справочник_Номенклатура')
    ->select('Ref_Key', 'Parent_Key', 'Description', 'IsFolder');
    ->orderBy(['IsFolder', 'desc'], 'Description');
    ->get();
if ($items->isOK()) {
    echo $items->count();
    foreach ($items as $item) {
        echo $item->Ref_Key . "\t" . $item->Description . "\n";
    }
    $folders = array_filter($items->values(), fn($item) => $item->IsFolder);
} else {
    echo $items->getOdataErrorCode() . ': ' . $items->getOdataErrorMessage();
}

// getValues()
try {
    $orders = $oData->query('Документ_ЗаказКлиента')
        ->where('Статус', 'Закрыт')
        ->getValues();
} catch (\Dakword\OData1C\Client\Exception\ODataClientException) {
    //
}
```

### получение одной сущности
```php
$item = $oData->query('Документ_ЗаказКлиента')->get('cbcf493e-55bc-11d9-848a-00112f43529a');

echo $item->Статус;

// описание набора ключевых значений для получения сущности 
// "InformationRegister_ДополнительныеСведения(Объект=fad620c2-c719-11e4-8ec3-bcaec56cc144, Объект_Type=StandardODATA.Catalog_Товары, Свойство_Key=guid'bd72d926-55bc-11d9-848a-00112f43529a')"
$item = $oData->query('Регистр сведений_ДополнительныеСведения')
    ->get([
        'Объект' => 'fad620c2-c719-11e4-8ec3-bcaec56cc144',
        'Объект_Type' => 'StandardODATA.Catalog_Товары',
        'Свойство_Key' => "guid'bd72d926-55bc-11d9-848a-00112f43529a'",
    ]);

// "InformationRegister_КурсыВалют(Period=datetime'2024-12-05T00:00:00',Валюта_Key=guid'9d5c4222-8c4c-11db-a9b0-00055d49b45e')"
$item = $oData->query('Регистр сведений_КурсыВалют')
    ->get([
        'Period' => "datetime'2028-12-05T00:00:00'",
        'Валюта_Key' => "guid'9d5c4222-8c4c-11db-a9b0-00055d49b45e'",
    ]);

// getFirst() 
$file = $oData->query('Регистр сведений', 'ДвоичныеДанныеФайлов')
    ->whereEq('Файл', Func::cast(Func::guid($fileRefKey), $ownerType))
->getFirst();
$file = $file ? base64_decode($file->ДвоичныеДанныеФайла_Base64Data) : false;

```

<hr>

### create(), update(), delete(), markDelete()
```php
// создание объекта 
$item = $oData->query('Справочник_Номенклатура')
    ->create([
        'IsFolder' => true,
        'Parent_Key' => 'baf54db6-7029-11e6-accf-0050568b35ac',
        'Description' => 'Макаронные изделия',
    ]);

// обновление объекта
$oData->query('Документ_ЗаказКлиента')
    ->update('bd72d924-55bc-11d9-848a-00112f43529a', [
        'Согласован' => true,
        'ДатаСогласования' => '2024-12-10T00:00:00',
        'ДатаОтгрузки' => '2024-12-24T00:00:00',
    ]);

// непосредственное удаление
$oData->query('Документ_ЗаказКлиента')
    ->delete('bd72d924-55bc-11d9-848a-00112f43529a');

// пометить на удаление
$oData->query('Документ_ЗаказКлиента')
    ->markDelete('bd72d924-55bc-11d9-848a-00112f43529a', true);
$oData->query('Документ_ЗаказКлиента')
    ->markDelete('bd72d924-55bc-11d9-848a-00112f43529a');

// снять пометку на удаление
$oData->query('Документ_ЗаказКлиента')
    ->markDelete('bd72d924-55bc-11d9-848a-00112f43529a', false);
$oData->query('Документ_ЗаказКлиента')
    ->unmarkDelete('bd72d924-55bc-11d9-848a-00112f43529a');
```

### post(), unPost()
```php
// неоперативное проведение
$oData->query('Документ_ЗаказКлиента')
    ->post('bd72d924-55bc-11d9-848a-00112f43529a');

// оперативное проведение
$oData->query('Документ_ЗаказКлиента')
    ->post('bd72d924-55bc-11d9-848a-00112f43529a', true);

// отмена проведения
$oData->query('Документ_ЗаказКлиента')
    ->unPost('bd72d924-55bc-11d9-848a-00112f43529a');
```

## Функции

```php
use \Dakword\OData1C\Client\Func;

$item = $oData->query('Регистр сведений_КурсыВалют')
    ->get([
        // 'Period' => "datetime'2028-12-05T00:00:00'",
        'Period' => Func::datetime(new \DateTime('2028-12-05')),
        // 'Валюта_Key' => "guid'9d5c4222-8c4c-11db-a9b0-00055d49b45e'",
        'Валюта_Key' => Func::guid('9d5c4222-8c4c-11db-a9b0-00055d49b45e'),
    ]);

$oData
    ->where(Func::subString('ИНН', 1, 2), '77')
    ->where(fn($query) => $query
        ->where(Func::startsWith('Производитель', 'ООО'), true)
        ->orWhere(Func::endsWith('Производитель', 'ООО'), true)
    )
    ->where('Объект', Func::cast(Func::guid($ownerRefKey), 'Document_ЗаказКлиента'))

    // ->whereGUID('Parent_Key', '00000000-0000-0000-0000-000000000000')
    ->whereGUID('Parent_Key', Func::guid())
    ->whereGUID('Parent_Key', Func::EMPTYGUID);
```