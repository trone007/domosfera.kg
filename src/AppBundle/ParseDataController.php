<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity as Entities;

class ParseDataController extends Controller
{
    /**
     * @Route("/update-vendors")
     */
    public function updateVendorsAction()
    {
        header('Content-type: text/plain');
        libxml_use_internal_errors(true);
        $configObj= simplexml_load_string('http://www.gallery.kg/api/%D0%B4%D0%BE%D0%BC%D0%BE%D1%81%D1%84%D0%B5%D1%80%D0%B0/%D0%BD%D0%B0%D1%81%D1%82%D1%80%D0%BE%D0%B9%D0%BA%D0%B8');
        echo 'Import regions...', PHP_EOL;
        $this->updateRegions($configObj->Регионы->Регион);
        echo 'Regions imported successfull', PHP_EOL;

        echo 'Import shops...', PHP_EOL;
        $this->updateShops($configObj->Магазины->Магазин);
        echo 'Shops imported successfull', PHP_EOL;

        echo 'Import organizations...', PHP_EOL;
        $this->updateOrganizations($configObj->Организации->Организация);
        echo 'Organizations imported successfull', PHP_EOL;


        $shops = $this->getDoctrine()->getRepository('AppBundle:Shop')->findAll();

        foreach ($shops as $shop) {
            set_time_limit(0);
            echo 'Import wallpapers for ' , $shop->getUuid() , ' shop...', PHP_EOL;

            $url = 'http://www.gallery.kg/api/'
                . urlencode('домосфера') . '/'
                . urlencode('товары') . '?'
                . urlencode('магазин') . '=' . $shop->getUuid() . '&'
                . urlencode('видНоменклатуры') . '='
                . urlencode('Обои');

            $wallpapers = @simplexml_load_file($url)->ЭлементКаталога;
            if (!$wallpapers) {

                echo 'no wallpapers to import', PHP_EOL;
                continue;
            }

            $this->updateWallpapers($wallpapers);

            echo 'Wallpapers imported successfull', PHP_EOL;
//            die;
        }

        exit;
    }

    /**
     * @Route("/update-images")
     */
    public function updateImagesAction()
    {
        $wallpapers = $this->getDoctrine()->getRepository('AppBundle:Wallpaper')->findAll();

        foreach ($wallpapers as $wallpaper) {

            $this->updateWallpapers($wallpapers);

            echo 'Wallpapers imported successfull', PHP_EOL;
//            die;
        }

        exit;
    }

    /**
     * @Route("/update-count")
     */
    public function updateVendorsCountAction()
    {
        header('Content-type: text/plain');

        $shops = $this->getDoctrine()->getRepository('AppBundle:Shop')->findAll();

        foreach ($shops as $shop) {
            set_time_limit(0);

            $url = 'http://www.gallery.kg/api/'
                . urlencode('домосфера') . '/'
                . urlencode('остатки') . '?'
                . urlencode('магазин') . '=' . $shop->getUuid() . '&'
                . urlencode('видНоменклатуры') . '='
                . urlencode('Обои');

            $count = @simplexml_load_file($url)->Остаток;

            $this->updateCount($count, $shop->getUuid());
            echo 'Wallpapers imported successfull', PHP_EOL;
//            die;
        }
        exit;
    }

    private function updateRegions($regions) {
        $doctrine = $this->getDoctrine();
        foreach($regions as $row) {
            if(!$region = $doctrine->getRepository('AppBundle:Region')->findOneByUuid($row->Ссылка)) {
                $region = new Entities\Region();
            }

            $region->setUuid($row->Ссылка);
            $region->setParentUuid($row->РодительСсылка);
            $region->setUrlName($row->UrlId);
            $region->setName($row->Наименование);

            $doctrine->getManager()->persist($region);
            $doctrine->getManager()->flush();
        }

    }

    private function updateShops($shops) {
        $doctrine = $this->getDoctrine();
        foreach($shops as $row) {
            if(!$shop = $doctrine->getRepository('AppBundle:Shop')->findOneByUuid($row->БуквенныйКод)) {
                $shop = new Entities\Shop();
            }

            $shop->setUuid($row->БуквенныйКод);
            $shop->setName($row->Наименование);
            $shop->setType($row->ВидМагазина);
            $shop->setPhoneNumber($row->ОсновнойТелефон);

            $regionChild = $doctrine->getRepository('AppBundle:Region')->findOneByUrlName($row->Регион);

            $shop->setRegionUuid($regionChild->getUuid());

            $doctrine->getManager()->persist($shop);
            $doctrine->getManager()->flush();
        }

    }

    private function updateOrganizations($organizations) {
        $doctrine = $this->getDoctrine();
        foreach($organizations as $row) {
            if(!$organization = $doctrine->getRepository('AppBundle:Organization')->findOneByUuid($row->Uuid)) {
                $organization = new Entities\Organization();
            }

            $organization->setUuid($row->Uuid);
            $organization->setName($row->Наименование);
            $organization->setCode($row->Код);

            $doctrine->getManager()->persist($organization);
            $doctrine->getManager()->flush();
        }

    }

    private function updateWallpapers($wallpapers) {
        $doctrine = $this->getDoctrine();

        foreach($wallpapers as $row) {
            if(!$wallpaper = $doctrine->getRepository('AppBundle:Wallpaper')->findOneBy([
                'uuid' => $row->Uuid,
                'shop' => $row->Магазин
            ])) {
                $wallpaper = new Entities\Wallpaper();
            }

            $wallpaper->setUuid($row->Uuid);
            $wallpaper->setVendorCode($row->Артикул);

            $wallpaper->setImage($row->Изображение);
//            $this->updateImage($row->Изображение, $row->ДатаИзмененияИзображения);

            $wallpaper->setSeamlessStructure($row->БесшовнаяСтруктура);
            $wallpaper->setPriceOld($row->ЦенаДоСкидки);
            $wallpaper->setPrice($row->Цена);
            $wallpaper->setCatalog($row->Каталог);
            $wallpaper->setNotebook($row->Тетрадь);
            $wallpaper->setMainNomenclature($row->ВидНоменклатурыОригинальный);
            $wallpaper->setNomenclature($row->ВидНоменклатуры);
            $wallpaper->setCountry($row->Страна);
            $wallpaper->setCurrency($row->Валюта);
            $wallpaper->setOrganization($row->Организация);
            $wallpaper->setShop($row->Магазин);
            $wallpaper->setPoints((float)$row->Балл);
            $wallpaper->setSpeed((float)$row->Скорость);
            $wallpaper->setSuccessfull((float)$row->Популярность);
            $wallpaper->setManufacturer($row->Производитель);
            $wallpaper->setDateTime(new \DateTime($row->ДатаСоздания));
            $wallpaper->setUnit($row->ЕдиницаИзмерения);
            $wallpaper->setMarketPlan((float)str_replace(",", ".", $row->КоэффициентМаркетПлана));

            foreach ($row->СвойстваФильтра->ЭлементСвойств as $filter) {
//                var_dump($filter);
                switch($filter->Свойство) {
                    case 'глиттер':
                        $wallpaper->setGlitter($filter->Значение == 'Да' ? true : false);
                        break;

                    case 'рисунок':
                        $wallpaper->setPicture($filter->Значение);
                        break;

                    case 'фактура':
                        $wallpaper->setTexture($filter->Значение);
                        break;

                    case 'основа':
                        $wallpaper->setBasis($filter->Значение);
                        break;

                    case 'цвет-1':
                        $wallpaper->setColor1($filter->Значение);
                        break;

                    case 'цвет-2':
                        $wallpaper->setColor2($filter->Значение);
                        break;

                    case 'цвет-3':
                        $wallpaper->setColor3($filter->Значение);
                        break;

                    case 'размер':
                        $wallpaper->setSize($filter->Значение);
                        break;
                }
            }

            $doctrine->getManager()->persist($wallpaper);
            $doctrine->getManager()->flush();
        }
    }

    private function updateCount($countObject, $shop) {
        $doctrine = $this->getDoctrine();

        foreach ($countObject as $count) {
            if (!$wallpaperCount = $this->getDoctrine()
                ->getRepository('AppBundle:WallpaperCount')
                ->findOneBy([
                    'shop' => $shop,
                    'wallpaperUuid' => $count->КодТовара
                ])
            ) {
                $wallpaperCount = new Entities\WallpaperCount();
            }

            $wallpaperCount->setCount((float)$count->ОстатокМагазина);
            $wallpaperCount->setTotalCount((float)$count->ОстатокОбщий);
            $wallpaperCount->setShop($shop);
            $wallpaperCount->setWallpaperUuid($count->КодТовара);

            $doctrine->getManager()->persist($wallpaperCount);
            $doctrine->getManager()->flush();
        }
    }

    private function updateImage($image, $imageDateTime){
        $doctrine = $this->getDoctrine();
        if (!$imageObj = $this->getDoctrine()
            ->getRepository('AppBundle:Images')
            ->findOneBy([
                'imageCode' => $image
            ])
        ) {
            $imageObj = new Entities\Images();
        } else {
            if ($imageObj->getDateTime() != new \DateTime($imageDateTime)) {
                return true;
            }
        }

        $imageObj->setImageCode($image);
        $imageObj->setImage(base64_encode(file_get_contents('http://www.gallery.kg/catimage/' . $image)));
        $imageObj->setDateTime(new \DateTime($imageDateTime));

        $doctrine->getManager()->persist($imageObj);
        $doctrine->getManager()->flush();
    }


}
