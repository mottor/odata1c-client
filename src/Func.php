<?php
declare(strict_types=1);

namespace Dakword\OData1C\Client;

use DateTime;

class Func
{
    const string EMPTYGUID = '00000000-0000-0000-0000-000000000000';

    /**
     * Формирование типа УникальныйИдентификатор (Edm.Guid)
     *
     * guid'12345678-1234-...'
     */
    static public function guid(?string $string = null): string
    {
        return "guid'". (is_null($string) ? self::EMPTYGUID : $string) . "'";
    }

    /**
     * Формирование типа ДатаВремя
     *
     * datetime'Y-m-dTH:i:s'
     */
    static public function datetime(DateTime $dateTime): string
    {
        return "datetime'{$dateTime->format('Y-m-d\TH:i:s')}'";
    }

    /**
     * Возвращает год из значения свойства переданного в параметре
     *
     * year(ДатаПроизводства)
     *
     * @param string $field Имя свойства со значением типа Edm.DateTime или Edm.DateTimeOffset
     */
    static function year(string $field): string
    {
        return "year({$field})";
    }

    /**
     * Номер квартала года, в котором находится значение свойства переданного в параметре
     *
     * quarter(ДатаПроизводства)
     *
     * @param string $field Имя свойства со значением типа Edm.DateTime
     */
    static function quarter(string $field): string
    {
        return "quarter({$field})";
    }

    /**
     * Возвращает месяц из значения свойства переданного в параметре
     *
     * month(ДатаПроизводства)
     *
     * @param string $field Имя свойства со значением типа Edm.DateTime или Edm.DateTimeOffset
     */
    static function month(string $field): string
    {
        return "month({$field})";
    }

    /**
     * Возвращает день из значения свойства переданного в параметре
     *
     * day(ДатаПроизводства)
     *
     * @param string $field Имя свойства со значением типа Edm.DateTime или Edm.DateTimeOffset
     */
    static function day(string $field): string
    {
        return "day({$field})";
    }

    /**
     * Возвращает значение часов из значения свойства переданного в параметре
     *
     * hour(ДатаПроизводства)
     *
     * @param string $field Имя свойства со значением типа Edm.DateTime или Edm.DateTimeOffset
     */
    static function hour(string $field): string
    {
        return "hour({$field})";
    }

    /**
     * Возвращает значение минут из значения свойства переданного в параметре
     *
     * minute(ДатаПроизводства)
     *
     * @param string $field Имя свойства со значением типа Edm.DateTime или Edm.DateTimeOffset
     */
    static function minute(string $field): string
    {
        return "minute({$field})";
    }

    /**
     * Возвращает значение секунд из значения свойства переданного в параметре
     *
     * second(ДатаПроизводства)
     *
     * @param string $field Имя свойства со значением типа Edm.DateTime или Edm.DateTimeOffset
     */
    static function second(string $field): string
    {
        return "second({$field})";
    }

    /**
     * Возвращает разность дат
     *
     * datedifference(Произведен, ГоденДо, ‘day’)
     *
     * @param string $type Единица разности дат: second, minute, hour, day, month, quarter, year
     */
    static function dateDifference(string $dateTime1, string $dateTime2, string $type): string
    {
        return "datedifference({$dateTime1}, {$dateTime2}, ‘{$type}’) ";
    }

    /**
     * Возвращает дату, полученную добавлением к значению dateTime значения count,
     * выраженное в единицах type
     *
     * dateadd(Произведен, ‘month’, 1)
     *
     * @param string $type Единица увеличения: second, minute, hour, day, month, quarter, year
     */
    static function dateAdd(string $dateTime, string $type, int $count): string
    {
        return "dateadd({$dateTime}, '{$type}', {$count})";
    }

    /**
     * Возвращает день недели
     *
     * dayofweek(ДатаПроизводства)
     */
    static function dayOfWeek(string $dateTime): string
    {
        return "dayofweek({$dateTime})";
    }

    /**
     * Возвращает день года
     *
     * dayofyear(ДатаПроизводства)
     */
    static function dayOfYear(string $dateTime): string
    {
        return "dayofyear({$dateTime})";
    }

    /**
     * Возвращает параметр, округленный до ближайшего целого числа
     *
     * round(Вес)
     */
    static function round(string $number): string
    {
        return "round({$number})";
    }

    /**
     * Возвращает true в том случае, если string1 является подстрокой string2
     *
     * substringof('string1', string2)
     */
    static public function substringOf(string $string1, string $string2): string
    {
        return "substringof('{$string1}', {$string2})";
    }

    /**
     * Возвращает true в том случае, если string1 начинается на string2
     *
     * startswith(string1, 'string2')
     */
    static public function startsWith(string $string1, string $string2): string
    {
        return "startswith({$string1}, '{$string2}')";
    }

    /**
     * Возвращает true в том случае, если string1 заканчивается на string2
     *
     * endswith(string1, 'string2')
     */
    static public function endsWith(string $string1, string $string2): string
    {
        return "endswith({$string1}, '{$string2}')";
    }

    /**
     * Возвращает подстроку
     * С двумя параметрами возвращается строка с позиции start и до конца строки.
     * С тремя параметрами возвращается подстрока, начиная с позиции start и длиной length.
     *
     * substring(string, int[, int])
     *
     * @param string $string Входная строка "'text'" или имя параметра "АдресПроживания"
     * @param int $start Начальная позиция
     * @param int $length Длинна
     * @return string Извлеченная часть
     */
    static public function subString(string $string, int $start, int $length = null): string
    {
        return "substring({$string}, {$start}" . (!is_null($length) ? ", {$length}" : '') . ')';
    }

    /**
     * Возвращает строку, являющуюся результатом конкатенации двух параметров
     *
     * concat(string1, string2)
     */
    static public function concat(string $string1, string $string2): string
    {
        return "concat({$string1}, {$string2})";
    }

    /**
     * Возвращает true, если значение string удовлетворяет шаблону template
     *
     * like(string, 'template')
     *
     * @param string $template Синтаксис шаблона аналогичен функции ПОДОБНО() языка запросов
     */
    static public function like(string $string, string $template): string
    {
        return "like({$string}, '{$template}')";
    }

    /**
     * Приведение к типу
     *
     * cast(РеквизитСоставной, 'Number')
     * cast(guid'0d4a79cb-9843-4147-bcd9-80ac3ca2b9c7', 'Document_ПриходнаяНакладная')
     */
    static public function cast(string $expr, string $type): string
    {
        return "cast({$expr}, '{$type}')";
    }

    /**
     * Сравнение типа объекта, на который ссылается параметр expr
     * с типом указанным в параметре type
     *
     * isof(Цена, ‘Number’)
     * isof(ДокументыПрихода, 'Document_ПриходнаяНакладная')
     *
     * @param string $type String, Number, Boolean, Date, Catalog_Товары ...
     */
    static public function isOf(string $expr, string $type): string
    {
        return "isof({$expr}, {$type})";
    }

}
