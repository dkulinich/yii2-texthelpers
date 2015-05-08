<?php

namespace dkulinich\texthelpers;

/**
 *
 * Хелпер для работы с текстом.
 *
 * @todo:хелпер в разработке.
 *
 * POST-параметры, используемые хелпером начинаются с префикса "json_".
 *
 */
class ExtCoreTextHelper extends yii\base\Component
{
    /**
     * минимальное количество слов,
     * @var integer
     */
    public static $minWordsCount = 1;

    /**
     * максимальное количество слов, с которыми будет
     * оперировать фнкция
     * @var integer
     */
    public static $maxWords = 128;


    /**
     * использовать "сухую обрезку", в случае
     * неукаладывания в погрешности
     * @var boolean
     */
    public static $enableDryCut = false;

    /**
     * максимальная погрешность между ожидаемой длиной
     * строки и получившимся результатом (в обратную
     * сторону)
     * @var integer
     */
    public static $maxDownImpression = 14;

    /**
     * использовать "сухую обрезку", в случае
     * неукаладывания в погрешности
     * @var integer
     */
    public static $maxLength = 75;


    public static $cyrTransliterates = array(
        "а"=>"a","А"=>"a",
        "б"=>"b","Б"=>"b",
        "в"=>"v","В"=>"v",
        "г"=>"g","Г"=>"g",
        "д"=>"d","Д"=>"d",
        "е"=>"e","Е"=>"e",
        "ж"=>"zh","Ж"=>"zh",
        "з"=>"z","З"=>"z",
        "и"=>"i","И"=>"i",
        "й"=>"y","Й"=>"y",
        "к"=>"k","К"=>"k",
        "л"=>"l","Л"=>"l",
        "м"=>"m","М"=>"m",
        "н"=>"n","Н"=>"n",
        "о"=>"o","О"=>"o",
        "п"=>"p","П"=>"p",
        "р"=>"r","Р"=>"r",
        "с"=>"s","С"=>"s",
        "т"=>"t","Т"=>"t",
        "у"=>"u","У"=>"u",
        "ф"=>"f","Ф"=>"f",
        "х"=>"h","Х"=>"h",
        "ц"=>"c","Ц"=>"c",
        "ч"=>"ch","Ч"=>"ch",
        "ш"=>"sh","Ш"=>"sh",
        "щ"=>"sch","Щ"=>"sch",
        "ъ"=>"","Ъ"=>"",
        "ы"=>"y","Ы"=>"y",
        "ь"=>"","Ь"=>"",
        "э"=>"e","Э"=>"e",
        "ю"=>"yu","Ю"=>"yu",
        "я"=>"ya","Я"=>"ya",
        "і"=>"i","І"=>"i",
        "ї"=>"yi","Ї"=>"yi",
        "є"=>"e","Є"=>"e"
    );

    // "корректное" обрезания заголовков и предисловий
    public static function cutTextAtWords($text, $max_length = NULL)
    {
        $text = trim($text);
        $max_length or $max_length = self::$maxLength;

        // если строка укладывается в в максимальную длину
        if(mb_strlen($text, 'utf8') <= $max_length) return $text;


        // строка режется на слова, чтобы постепенно
        // добавлять к результату по одному слову, до тех
        // пор, пока не будет нащупан предел
        $matched_words = mb_split("[\f\n\r\t ]", $text, self::$maxWords);

        // подсчёт использованных слов
        $used_words_count = 0;

        // строка на выходе
        $result_string = NULL;



        // добавляются слова, до тех пор, пока не нащупан предел
        foreach($matched_words as $index => $word)
        {
            $str_len = mb_strlen($result_string, 'utf8');


            // длина строки, если добавить слово
            $regular_string_length	= $str_len
                + ($str_len ? 1 : 0)
                + mb_strlen($word, 'utf8');


            // ** нашли предел

            // предел игнорируется, если итоговая длина строки получилась
            // слишком короткой
            if( !(($str_len + self::$maxDownImpression) < $max_length) )

                // также предел игнорируется, если итоговое количество
                // слов получилось длинее
                if(	($regular_string_length > $max_length) &&
                    ($index >= self::$minWordsCount)
                )
                    break;


            // ** продолжение

            // добавляем это слово и идём дальше
            $result_string .= ($str_len ? " " : NULL) .$word;
            $used_words_count = $index;
        }


        $str_len = mb_strlen($result_string, 'utf8');


        // если требуется принудительно обрубить все погрешности
        if(self::$enableDryCut)

            // обрезание выхода за пределы
            if((int)$str_len > (int)$max_length) $result_string = mb_substr($result_string, 0, $max_length, 'utf8');


        // последний символ не может быть незакрывающим
        // знаком препинания
        if(preg_match("/^[«\(\,\{\[]$/", mb_substr($result_string, -1, 1, 'utf8')))

            $result_string = mb_substr($result_string, 0, mb_strlen($result_string)-1, 'utf8');

        return $result_string . (mb_strlen($result_string, 'utf8') < mb_strlen($text, 'utf8') ? "..." : NULL);
    }

    public static function transliterate_cyr($text, $replaceWhitespaces = '_', $acceptedSymbols = '0-9a-zA-Z_'){

        // замена пробелов символами подчеркивания
        if($replaceWhitespaces)
            $text = preg_replace("/\s/", $replaceWhitespaces, $text);

        // обрезка всех левых символов
        if($acceptedSymbols !== false)
            $text = preg_replace("/[^А-Яа-яЁё$acceptedSymbols]+/u", '', $text);

        return strtr($text, self::$cyrTransliterates);
    }

}
