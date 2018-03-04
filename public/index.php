<?php

/*
 * To-Do:
 * - Форма для загрузки файлов
 * - Распаковка файлов во временную папку
 * - Исправление файла Document.xml
 * - Создание архива с новым файлом, добавление к оригиналу fix
 * - Выдача клиенту
 *
 * - Библиотека для работы с ZIP
 * - Config
 * - MVC (Yii2)
 * - Безопасность temp
 * - Консольное приложение
*/

const UPLOAD_FILE_NAME = "../upload/Document.xml";

class Fix
{
    public $fileName;
    public $content;
    public $pattern = '/(䩴[^"]+)/';
    public $replace = "";


    public function __construct($fileName = UPLOAD_FILE_NAME)
    {
        $this->fileName = $fileName;
        $this->run();
    }

    public function readFile()
    {
        // file — Читает содержимое файла и помещает его в массив
        // https://secure.php.net/manual/ru/function.file.php
        $this->content = file($this->fileName);
        // file_get_contents — Читает содержимое файла в строку
        // https://secure.php.net/manual/ru/function.file-get-contents.php
//        $this->content = file_get_contents($this->fileName);
    }

    public function fix()
    {
        // mb_ereg_replace
//        $pattern = '/(䩴..............)/'; // Work
//        $pattern = '/(䩴\W+)/';
//        $input = $this->content;
//        $replace = 'PRIVET" ';
        // preg_replace — Выполняет поиск и замену по регулярному выражению
        // https://secure.php.net/manual/ru/function.preg-replace.php
        $this->content = preg_replace($this->pattern, $this->replace, $this->content);
        // preg_grep — Возвращает массив вхождений, которые соответствуют шаблону
        // https://secure.php.net/manual/ru/function.preg-grep.php
//        $this->content = preg_grep($pattern, $input);
    }

    public function writeFile()
    {
        $fileName = pathinfo($this->fileName);
        $newFileName = "./temp/" . $fileName["filename"] . "_fix." . $fileName["extension"];
        file_put_contents($newFileName, $this->content);
    }

    public function run()
    {
        $this->readFile();
        $this->fix();
        $this->writeFile();
        // implode — Объединяет элементы массива в строку
        // https://secure.php.net/manual/ru/function.implode.php
        echo implode($this->content);
//                var_dump($this->content);
    }

}

$app = new Fix(UPLOAD_FILE_NAME);