<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Title");
?>
<?
$arViewed = array();
$basketUserId = (int)CSaleBasket::GetBasketUserID(false);
if ($basketUserId > 0){
   $viewedIterator = \Bitrix\Catalog\CatalogViewedProductTable::getList(array(
      'select' => array('PRODUCT_ID', 'ELEMENT_ID'),
      'filter' => array('=FUSER_ID' => $basketUserId, '=SITE_ID' => SITE_ID),
      'order' => array('DATE_VISIT' => 'DESC'),
      'limit' => 10
   ));

   while ($arFields = $viewedIterator->fetch()){
      $arViewed[] = $arFields['ELEMENT_ID'];
   }
}
echo '<pre>';
var_dump($arViewed);
echo '</pre>';
?>



<?
$countViewedProducts = 0;
$GLOBALS['arViewedProducts'] = array();
if(\Bitrix\Main\Loader::includeModule("catalog") && \Bitrix\Main\Loader::includeModule("sale"))
{
   $arFilter["FUSER_ID"] = CSaleBasket::GetBasketUserID();
   if(\Bitrix\Main\Config\Option::get("sale", "viewed_capability", "") == "Y")
   {
      $viewedIterator = \Bitrix\Catalog\CatalogViewedProductTable::getList(
         array(
            "filter" => $arFilter,
            "select" => array(
               "ID", "PRODUCT_ID"
            ),
            "order" => array("DATE_VISIT" => "DESC"),
         )
      );

      while($row = $viewedIterator->fetch())
      {
         $GLOBALS['arViewedProducts'][] = $row['PRODUCT_ID'];
         $countViewedProducts++;
      }
   }
}

echo $countViewedProducts;
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>