сверить таблицу b_file (взять оттуда ID файлов) с таблицами b_iblock_element и свойствами (может там еще файлы есть)

и далее через цикл и полученные id , всё удалим из этой таблицы


/*далее код который бы передалл */
<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$deleteFiles = ‘yes’;
$saveBackup = ‘yes’;
global $USER;
if (!$USER->IsAdmin()) {
echo "Одумайся или авторизуйся...";
return;
}
$time_start = microtime(true);
echo '
';
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
$deleteFiles = 'yes'; //Удалять ли найденые файлы yes/no
$saveBackup = 'no'; //Создаст бэкап файла yes/no
//Папка для бэкапа
$patchBackup = $_SERVER['DOCUMENT_ROOT'] . "/upload/zixnru_Backup/";
//Целевая папка для поиска файлов
$rootDirPath = $_SERVER['DOCUMENT_ROOT'] . "/upload/iblock";
//Создание папки для бэкапа
if (!file_exists($patchBackup)) {
CheckDirPath($patchBackup);
}
// Получаем записи из таблицы b_file
$arFilesCache = array();
$result = $DB->Query('SELECT FILE_NAME, SUBDIR FROM b_file WHERE MODULE_ID = "iblock"');
while ($row = $result->Fetch()) {
$arFilesCache[$row['FILE_NAME']] = $row['SUBDIR'];
}
$hRootDir = opendir($rootDirPath);
$count = 0;
$contDir = 0;
$countFile = 0;
$i = 1;
$removeFile=0;
while (false !== ($subDirName = readdir($hRootDir))) {
if ($subDirName == '.' || $subDirName == '..') {
continue;
}
//Счётчик пройденых файлов
$filesCount = 0;
$subDirPath = "$rootDirPath/$subDirName"; //Путь до подкатегорий с файлами
$hSubDir = opendir($subDirPath);
while (false !== ($fileName = readdir($hSubDir))) {
if ($fileName == '.' || $fileName == '..') {
continue;
}
$countFile++;
if (array_key_exists($fileName, $arFilesCache)) { //Файл с диска есть в списке файлов базы - пропуск
$filesCount++;
continue;
}
$fullPath = "$subDirPath/$fileName"; // полный путь до файла
$backTrue = false; //для создание бэкапа
if ($deleteFiles === 'yes') {
if (!file_exists($patchBackup . $subDirName)) {
if (CheckDirPath($patchBackup . $subDirName . '/')) { //создал поддиректорию
$backTrue = true;
}
} else {
$backTrue = true;
}
if ($backTrue) {
if ($saveBackup === 'yes') {
CopyDirFiles($fullPath, $patchBackup . $subDirName . '/' . $fileName); //копия в бэкап
}
}
//Удаление файла
if (unlink($fullPath)) {
$removeFile++;
echo "Удалил: " . $fullPath . '
';
}
} else {
$filesCount++;
echo 'Кандидат на удаление - ' . $i . ') ' . $fullPath . '
';
}
$i++;
$count++;
unset($fileName, $backTrue);
}
closedir($hSubDir);
//Удалить поддиректорию, если удаление активно и счётчик файлов пустой - т.е каталог пуст
if ($deleteFiles && !$filesCount) {
rmdir($subDirPath);
}
$contDir++;
}
if ($count < 1) {
echo 'Не нашёл данных для удаления
';
}
if ($saveBackup === 'yes') {
echo 'Бэкап файлов поместил в: ' . $patchBackup . '
';
}
echo '<br>Всего файлов удалил: ' . $removeFile . '
';
echo '<br>Всего файлов в ' . $rootDirPath . ': ' . $countFile . '
';
echo '<br>Всего подкаталогов в ' . $rootDirPath . ': ' . $contDir . '
';
echo '<br>Всего записей в b_file: ' . count($arFilesCache) . '
';
closedir($hRootDir);
echo '
'; $time_end = microtime(true); $time = $time_end - $time_start; echo "Время выполнения $time секунд\n"; 
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
