## MindMap fix "Illegal xml character" error

Данная утилита была написана мною для автоматизациии исправления ошибки "Illegal xml character", которая возникает при работе с mindmap-картами в Mindjet MindManager 10 for Mac. На официальном сайте даётся краткая инструкция по ручному исправлению ошибки (приведена ниже), но это далеко не всегда удобно, т.к. ошибка достаточно часто возникает и не позволяет каким-либо образом открыть готовую карту, даже в частичном виде. В перспективе планирую выложить скрипт на веб-сервер для помощи в решении сложившейся проблемы.

P.S. На данный момент код написан очень коряво, в первую очередь писался для быстрого решения задачи, в дальнейшем планирую провести рефакторинг, сделать в качестве отдельного модуля под Yii2.

#### How to find source of "(1:133819) Illegal xml character" error? (Manual method)
https://community.mindjet.com/mindjet/topics/how-to-find-source-of-1-133819-illegal-xml-character-error

1. Change the file extension of the Map from .mmap to .zip
2. Extract the Zip archive.
3. Use a XML editor to edit the Document.xml file. A good & free one is notepad++
4. Locate the illegal characters and remove them, then save the changes.
5. Place the Document.xml file back in the unzipped archive, then zip it.
6. Change the file extension back to .mmap