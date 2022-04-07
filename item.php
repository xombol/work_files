<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Title");
?>
<?
use \Bitrix\Main\Loader;
// Проверяем подключены ли модули
if (!Loader::includeModule('iblock') || !Loader::includeModule('catalog'))
{
    // завершаем работу скрипта
    die('Ошибка при загрузке модуля ифноблока или каталога');
}

$IBlockOffersCatalogId = 3; // ID инфоблока предложений (должен быть торговым каталогом)
$productName = "Товар1"; // наименование товара
$offerName = "Торговое предложение1"; // наименование торгового предложения
$offerPrice = 100.50; // Цена торгового предложения

// Методы класса -> CCatalog,
// Если инфоблок с кодом $ID не существует или не является торговым каталогом, вернет false. Иначе возвращает ассоциативный массив с ключами:
$arCatalog = CCatalog::GetByID($IBlockOffersCatalogId);

$IBlockCatalogId = $arCatalog['PRODUCT_IBLOCK_ID']; // ID инфоблока товаров
$SKUPropertyId = $arCatalog['SKU_PROPERTY_ID']; // ID свойства в инфоблоке предложений типа "Привязка к товарам (SKU)"

$obElement = new CIBlockElement();
$arFields = array(
    'NAME' => $productName,
    'IBLOCK_ID' => $IBlockCatalogId,
    'ACTIVE' => 'Y',
	'CODE' => Cutil::translit($productName,"ru",array("replace_space"=>"-","replace_other"=>"-"))
);
$productId = $obElement->Add($arFields); // добавили товар, получили ID

if ($productId)
{
    $obElement = new CIBlockElement();
    // свойства торгвоого предложения
    $arOfferProps = array(
        $SKUPropertyId => $productId,
    );
    $arOfferFields = array(
        'NAME' => $offerName,
        'IBLOCK_ID' => $IBlockOffersCatalogId,
        'ACTIVE' => 'Y',
        'PROPERTY_VALUES' => $arOfferProps,
		'CODE' => Cutil::translit($offerName,"ru",array("replace_space"=>"-","replace_other"=>"-"))
    );

    $offerId = $obElement->Add($arOfferFields); // ID торгового предложения

    if ($offerId)
    {
        // добавляем как товар и указываем цену
        $catalogProductAddResult =	CCatalogProduct::Add(array(
            "ID" => $offersId,
            "VAT_INCLUDED" => "Y", //НДС входит в стоимость
        ));
		print_r($offerPrice);
        if ($catalogProductAddResult && !CPrice::SetBasePrice($offerId, $offerPrice, "RUB"))
            throw new Exception("Ошибка установки цены торгового предложения \"{$offerId}\"");
        else
            throw new Exception("Ошибка добавления параметров торгового предложения \"{$offerId}\" в каталог товаров");
    }
    else
    {
        throw new Exception("Ошибка добавления торгового предложения: " . $obElement->LAST_ERROR);
    }
}
else
{
    throw new Exception("Ошибка добавления товара: " . $obElement->LAST_ERROR);
}




?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>