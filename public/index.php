<?php

/*
 * To-Do:
 * + Ошибки при работе с файлами на кириллице
 * + Форма для загрузки файлов
 * + Распаковка файлов во временную папку
 * + Исправление файла Document.xml
 * + Создание архива с новым файлом, добавление к оригиналу fix
 * + Выдача клиенту
 * - Рефакторинг
 *
 * + Библиотека для работы с ZIP
 * - Config
 * - MVC (Yii2)
 * - Безопасность temp (права на папки, соответствие типу)
 * - Консольное приложение
 * - Тесты
*/

const DIR_TMP = "./temp/";

class Fix
{
    public $fileName;
    public $originalFileName;
    public $tmpFileName;
    public $newFileName;
    public $rndDirName;
    public $content;
    public $pattern = '/(䩴[^"]+)/';
    public $replace = "";


    public function __construct()
    {
        $this->fileName = $this->timeStampFileName();
        $this->originalFileName = $_FILES['file']['name'];
        $this->tmpFileName = $_FILES['file']['tmp_name'];
        $this->run();
    }

    public function timeStampFileName()
    {
        return date("Y-m-d_H-i-s") . "_fix.mmap";
    }

    public function readFile()
    {
        // file — Читает содержимое файла и помещает его в массив
        // https://secure.php.net/manual/ru/function.file.php
        $this->content = file($this->rndDirName . "Document.old");
    }

    public function fix()
    {
        // preg_replace — Выполняет поиск и замену по регулярному выражению
        // https://secure.php.net/manual/ru/function.preg-replace.php
        $this->content = preg_replace($this->pattern, $this->replace, $this->content);
    }

    public function writeFile()
    {
        // $fileName = pathinfo($this->rndDirName . "Document.xml");
        // $newFileName = $this->rndDirName . $fileName["filename"] . "_fix." . $fileName["extension"];
        file_put_contents($this->rndDirName . "Document.xml", $this->content);
    }

    public function rndDirCreate($fileName)
    {
        $this->rndDirName = DIR_TMP . md5($fileName . time()) . "/";
        mkdir($this->rndDirName, 0644);
    }

    public function unZip()
    {
        $zip = new ZipArchive;
        $res = $zip->open($this->newFileName);
        if ($res === true) {
            $zip->extractTo($this->rndDirName, array('Document.xml'));
            $zip->close();
            echo '<br>Разархивация прошла успешно!';
        } else {
            echo 'Ошибка разархивации!';
        }
    }

    public function replaceZip()
    {
        $zip = new ZipArchive;
        $res = $zip->open($this->newFileName);
        if ($res === true) {
            // Удалить старый файл из Zip
            $zip->deleteName("Document.xml");
            // Добавить исправленный файл в Zip
            $zip->addFile($this->rndDirName . "Document.xml", "Document.xml");
            $zip->close();
            echo '<br>Архивация прошла успешно!';
        } else {
            echo 'Ошибка архивации!';
        }
    }

    public function viewUrl() {
        echo '<br>Исправление файла прошло успешно!
        <hr>Загруженный файл ' . $this->originalFileName . ' был переименован в ' . $this->fileName . '
        <br>Ссылка для скачивания (доступно 24 часа):
        <a href="' . $this->newFileName . '">' . $this->fileName . '</a>
        <hr>
        <a href="../../">Загрузить ещё 1 файл!</a> 
        ';
    }

    public function run()
    {
        // Создаем рандомный каталог в temp
        $this->rndDirCreate($this->fileName);
        $this->newFileName = $this->rndDirName . $this->fileName;

        // В него копируем оригинальный файл MMAP
        copy($this->tmpFileName, $this->newFileName);

        // Извлекаем Document.xml из MMAP
        $this->unZip();
        rename($this->rndDirName . "Document.xml", $this->rndDirName . "Document.old");

        // Вносим изменения в Document.xml
        $this->readFile();
        $this->fix();
        $this->writeFile();

        // Заменям старый файл на исправленный
        $this->replaceZip();

        // Отображаем текст и ссылку для скачивания
        $this->viewUrl();

        // Изменяем режим доступа к файлам
        chmod($this->newFileName, 0444);
    }

}

if ($_SERVER['REQUEST_METHOD'] == "POST"
    && $_FILES['file']['type'] == "application/vnd.mindjet.mindmanager"
    && $_FILES['file']['size'] < 10485760) {
//    var_dump($_SERVER['REQUEST_METHOD']);
//    var_dump($_FILES);
    $app = new Fix();
} else {
    echo '
        <b>Загрузка MindMap файла (*.mmap) < 10Мб:</b>
            <form action="" enctype="multipart/form-data" method="post">
                <input type="file" name="file" accept="application/vnd.mindjet.mindmanager"/>
                <input type="submit" value="Загрузить"/>
            </form>
    ';
}