<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Title");
?>
События на сохранение заказа
https://dev.1c-bitrix.ru/api_d7/bitrix/sale/events/order_saved.php
OnSaleOrderSaved - Происходит в конце сохранения, когда заказ и все связанные сущности уже сохранены.

я бы описал в ловил это событие в init.php 
далле, т.к есть встроенный компонет на просмотренные товары, то обращался бы к этой таблице \Bitrix\Catalog\CatalogViewedProductTable
 по id пользователя который освершил зака, тем замым выведу последние 10  товаров, при выборке указываем нужную нас сортировку ASC 


до всего этого, в момоент установки модуля, в самом модуле прописал 

    function InstallDB()
    {
        Loader::includeModule($this->MODULE_ID);
        Base::getInstance('\Quest\Num\ResTable')->createDbTable();
    }
{{следовательно запрос sql }}
то есть мы создали таблицу и если вернуться к моменту выше, то все данные пушим в эту таблицу 

а уже в настройках модуля, когда он будет установлен , делем там в который и выведем все необходимые нам данные 




<?


$arViewed = array();
$basketUserId = (int)CSaleBasket::GetBasketUserID(false);
if ($basketUserId > 0){
   $viewedIterator = \Bitrix\Catalog\CatalogViewedProductTable::getList(array(
      'select' => array('PRODUCT_ID', 'ELEMENT_ID'),
      'filter' => array('=FUSER_ID' => $basketUserId, '=SITE_ID' => SITE_ID),
      'order' => array('DATE_VISIT' => 'ASC'),
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




<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
