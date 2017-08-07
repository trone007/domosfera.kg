<?php

namespace AppBundle\Controller;

use AppBundle\OData\ODataClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity as Entities;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * Котроллер отвечает за парсинг данных из 1С и выгрузку данных в 1С.
 * @author scouserlfc91@gmail.com
 */
class ParseDataController extends Controller
{

    private function importCollections()
    {
        $doctrine = $this->getDoctrine();
        $em = $this->getDoctrine()->getManager();
        $conn = $doctrine->getConnection();
        $conn->query('START TRANSACTION;');
        $conn->query('TRUNCATE collection_group RESTART IDENTITY;');
        $conn->query('COMMIT;');

        $conn->query('START TRANSACTION;');
        $conn->query('TRUNCATE  collection RESTART IDENTITY CASCADE;');
        $conn->query('COMMIT;');

        $vendors = $doctrine->getRepository('AppBundle:ComplectData')->findAll();
        $catalogs = [];

        echo count($vendors);

        foreach ($vendors as $vendor) {
            if($vendor->getCollectionCode()) {
                $collections[$vendor->getCollectionCode()]['vendors'][] = $vendor->getVendorCode();
                $collections[$vendor->getCollectionCode()]['style'] = $vendor->getStyle();
            }
        }

        foreach ($collections as $collectionName => $collection) {
            $collectionDB = new Entities\Collection();

            $collectionDB->setName($collectionName);
            $collectionDB->setProperty($collection['style']);

            $em->persist($collectionDB);

            foreach ($collection['vendors'] as $vendor) {
                $collectionGroup = new Entities\CollectionGroup();
                $collectionGroup->setCompanion($collectionDB);
                $collectionGroup->setVendorCode($vendor);

                $doctrine->getManager()->persist($collectionGroup);
            }
            $em->flush();
            $em->clear();
        }
        echo 'successfull importing collections';
    }
  private function importCompanions()
    {
        $doctrine = $this->getDoctrine();
        $em = $this->getDoctrine()->getManager();
        $conn = $doctrine->getConnection();
        $conn->query('START TRANSACTION;');
        $conn->query('TRUNCATE companion_group RESTART IDENTITY;');
        $conn->query('COMMIT;');

        $conn->query('START TRANSACTION;');
        $conn->query('TRUNCATE  companion RESTART IDENTITY CASCADE;');
        $conn->query('COMMIT;');

        $vendors = $doctrine->getRepository('AppBundle:ComplectData')->findAll();

        foreach ($vendors as $vendor) {
            if(!empty($vendor->getCompanion())) {
                $companionDB = new Entities\Companion();

                $em->persist($companionDB);

                $companionDB->setName($vendor->getManufacturer() . '/' . $vendor->getCatalog() . '/' . $companionDB->getId());
                $companionDB->setVendorCode($vendor->getVendorCode());

                $companions = json_decode($vendor->getCompanion());

                foreach ($companions as $companion) {
                    $companionGroup = new Entities\CompanionGroup();
                    $companionGroup->setCompanionCode($companionDB);
                    $companionGroup->setVendorCode($companion);

                    $doctrine->getManager()->persist($companionGroup);
                }
                $em->flush();
                $em->clear();
            }
        }
        echo 'successfull importing companions';
    }
  private function importComplects()
    {
        $doctrine = $this->getDoctrine();
        $em = $this->getDoctrine()->getManager();
        $conn = $doctrine->getConnection();
        $conn->query('START TRANSACTION;');
        $conn->query('TRUNCATE complect_group RESTART IDENTITY;');
        $conn->query('COMMIT;');

        $conn->query('START TRANSACTION;');
        $conn->query('TRUNCATE complect RESTART IDENTITY CASCADE;');
        $conn->query('COMMIT;');

        $vendors = $doctrine->getRepository('AppBundle:ComplectData')->findAll();

        foreach ($vendors as $vendor) {
            if(!empty($vendor->getComplect())) {
                $complectDB = new Entities\Complect();
                $em->persist($complectDB);
                $complectDB->setName($vendor->getManufacturer() . '/' . $vendor->getCatalog() . '/' . $complectDB->getId());
                $complectDB->setVendorCode($vendor->getVendorCode());

                if($vendor->getComplect() != 'Самостоятельный') {
                    $companionGroup = new Entities\ComplectGroup();
                    $companionGroup->setComplectCode($complectDB);
                    $companionGroup->setVendorCode($vendor->getComplect());

                    $em->persist($companionGroup);
                }

                $em->flush();
                $em->clear();
            }
        }
        echo 'successfull importing companions';
    }

    public function importNotebooks()
    {
        $doctrine = $this->getDoctrine();
        $em = $this->getDoctrine()->getManager();
        $conn = $doctrine->getConnection();
        $conn->query('START TRANSACTION;');
        $conn->query('TRUNCATE notebook_group RESTART IDENTITY;');
        $conn->query('COMMIT;');

        $conn->query('START TRANSACTION;');
        $conn->query('TRUNCATE  notebook RESTART IDENTITY CASCADE;');
        $conn->query('COMMIT;');

        $vendors = $doctrine
            ->getRepository('AppBundle:ComplectData')
            ->createQueryBuilder('cd')
            ->select('cd.catalog, cd.notebook, cd.vendorCode, cd.pictureFirst, cd.pictureSecond')
            ->getQuery()
            ->getResult();
        $notebooks = [];
        $total = 0;

        foreach ($vendors as $vendor) {
            if($vendor['notebook']) {

                $notebooks[$vendor['notebook']]['vendors'][] = $vendor['vendorCode'];
                $notebooks[$vendor['notebook']]['pictureFirst'] = $vendor['pictureFirst'];
                $notebooks[$vendor['notebook']]['pictureSecond'] = $vendor['pictureSecond'];
            }
        }

        foreach ($notebooks as $notebookName => $notebook) {
            $notebookDB = new Entities\Notebook();

            $notebookDB->setName($notebookName);
            $notebookDB->setProperty($notebook['pictureFirst']);
            $notebookDB->setPropertySecond($notebook['pictureSecond']);

            $em->persist($notebookDB);

            foreach ($notebook['vendors'] as $vendor) {
                $notebookGroup = new Entities\NotebookGroup();

                $notebookGroup->setCompanion($notebookDB);
                $notebookGroup->setVendorCode($vendor);

                $doctrine->getManager()->persist($notebookGroup);
            }
            $em->flush();
            $em->clear();
        }
        echo 'successful';
    }

    /**
     * @Route("/update-vendors")
     */
    public function updateVendorsAction()
    {
        header('Content-type: text/plain');
        libxml_use_internal_errors(true);
        $configObj= @simplexml_load_file('http://www.gallery.kg/api/%D0%B4%D0%BE%D0%BC%D0%BE%D1%81%D1%84%D0%B5%D1%80%D0%B0/%D0%BD%D0%B0%D1%81%D1%82%D1%80%D0%BE%D0%B9%D0%BA%D0%B8');
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

        $conn = $this
            ->getDoctrine()
            ->getManager()
            ->getConnection();
        $conn->query('START TRANSACTION;');
        $conn->query('TRUNCATE wallpaper RESTART IDENTITY;');
        $conn->query('COMMIT;');
        foreach ($shops as $shop) {
            set_time_limit(0);
            echo 'Import wallpapers for ' , $shop->getUuid() , ' shop...', PHP_EOL;

            $url = 'http://www.gallery.kg/api/'
                . urlencode('домосфера') . '/'
                . urlencode('товары') . '?'
                . urlencode('магазин') . '=' . $shop->getUuid();

            $wallpapers = simplexml_load_file($url, 'SimpleXMLElement', LIBXML_ERR_WARNING);

            if (!$wallpapers) {

                echo 'no wallpapers to import', PHP_EOL;
                continue;
            }

            $this->updateWallpapers($wallpapers->ЭлементКаталога);

            echo 'Wallpapers imported successfull', PHP_EOL;
            ob_end_flush();
        }
        $conn->close();

        exit;
    }

    /**
     * @Route("/update-notebooks")
     */
    public function updateNotebooksAction()
    {
        header('Content-type: text/plain');
        libxml_use_internal_errors(true);
        $configObj= @simplexml_load_file('http://www.gallery.kg/api/%D0%B4%D0%BE%D0%BC%D0%BE%D1%81%D1%84%D0%B5%D1%80%D0%B0/%D0%BD%D0%B0%D1%81%D1%82%D1%80%D0%BE%D0%B9%D0%BA%D0%B8');

        echo 'Import notebooks...', PHP_EOL;
        $this->updateNotebooks($configObj->Каталоги->Каталог);
        echo 'Notebooks imported successfull', PHP_EOL;

        exit;
    }

    /**
     * @Route("/update-images")
     */
    public function updateImagesAction()
    {
        $wallpapers = $this->getDoctrine()->getRepository('AppBundle:Wallpaper')
            ->createQueryBuilder('s')
            ->select('DISTINCT s.image as image')
            ->where('s.nomenclature = :nom')
            ->setParameter('nom', 'Обои')
            ->getQuery()
            ->getResult();;
        foreach ($wallpapers as $wallpaper) {
            $this->updateImage($wallpaper);
            unset($wallpaper);
        }

        echo 'Images imported successfull', PHP_EOL;

        exit;
    }

    /**
     * @Route("/update-count")
     */
    public function updateVendorsCountAction()
    {
        header('Content-type: text/plain');

        $shops = $this->getDoctrine()->getRepository('AppBundle:Shop')->findAll();
        $nomenclature = ['Обои', 'Фотообои', 'Лепнина', 'Кафель'];

        foreach ($shops as $shop) {
            set_time_limit(0);
            foreach ($nomenclature as $nom) {
                $url = 'http://www.gallery.kg/api/'
                    . urlencode('домосфера') . '/'
                    . urlencode('остатки') . '?'
                    . urlencode('магазин') . '=' . $shop->getUuid() . '&'
                    . urlencode('видНоменклатуры') . '='
                    . urlencode($nom);

                $count = @simplexml_load_file($url)->Остаток;

                $this->updateCount($count, $shop->getUuid());
            }
            echo 'Wallpapers imported successfull', PHP_EOL;
//            die;
        }
        exit;
    }

    /**
     * @Route("/update-complect")
     */
    public function updateComplectsAction()
    {
        header('Content-type: text/plain');

        set_time_limit(0);
//        var_dump(1);die;
//        $url = 'http://www.gallery.kg/api/'
//            . urlencode('домосфера') . '/'
//            . urlencode('комплекты');
//        $count = @simplexml_load_file($url);
//
//        $this->updateComplectData($count);

//        $this->importCollections();
        $this->importComplects();
//        $this->importNotebooks();
//        $this->importCompanions();
        exit;
    }
    /**
     * @Route("/upload-data")
     */
    public function uploadDataAction()
    {
        header('Content-type: application/xml');

        $doctrine = $this->getDoctrine();

        $conn = $doctrine->getManager()->getConnection();

        $sql ='SELECT
                w.vendor_code as "vendorCode",
                array_to_json(cc.collection) as "collection",
                nn.notebook as "notebook",
                nn.property as "picture",
                nn.property_second as "pictureSecond",
                array_to_json(ct.complect) as "complect",
                array_to_json(cn.companion) as "companion",
                w.notebook_uuid as "notebook_uuid",
                w.catalog_uuid as "catalog_uuid",
                w.catalog
                FROM complect_data w
                LEFT JOIN (
                  SELECT array_agg(c.name) as collection, cg.vendor_code FROM collection c
                  INNER JOIN collection_group cg ON  cg.companion_id = c.id
                  GROUP BY cg.vendor_code
                  ) cc ON cc.vendor_code = w.vendor_code
                LEFT JOIN (
                  SELECT n.name as notebook, cg.vendor_code, n.property, n.property_second  FROM notebook n
                  INNER JOIN notebook_group cg ON  cg.companion_id = n.id
                  ) nn ON nn.vendor_code = w.vendor_code
                LEFT JOIN (
                  SELECT array_agg(cg.vendor_code) as "companion", c.vendor_code FROM companion c
                  INNER JOIN companion_group cg ON  cg.companion_code = c.id
                  GROUP BY c.vendor_code
                  ) cn ON cn.vendor_code = w.vendor_code
                LEFT JOIN (
                  SELECT array_agg(cg.vendor_code) as "complect", c.vendor_code FROM complect c
                  INNER JOIN complect_group cg ON  cg.complect_code = c.id
                  GROUP BY c.vendor_code
                  ) ct ON ct.vendor_code = w.vendor_code
                ORDER BY w.vendor_code';

        try {

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $vendors = $stmt->fetchAll();
            $return = [];
            $return['Element'] = [];

            foreach ($vendors as $vendor) {
                $colls = json_decode($vendor['collection']);
                $collections = [];

                foreach ($colls as $collection) {
                    $collections['Collection'][] = $collection;
                }
                $compts = json_decode($vendor['complect']);
                $complects = [];

                foreach ($compts as $complect) {
                    $complects['Complect'][] = $complect;
                }
                $comps = json_decode($vendor['companion']);
                $companions = [];

                foreach ($comps as $companion) {
                    $companions['Companion'][] = $companion;
                }
                $return['Element'][] = [
                    'SKU' => $vendor['vendorCode'],
                    'Notebook' => trim($vendor['notebook']),
                    'Picture' => trim($vendor['picture']),
                    'PictureSecond' => trim($vendor['pictureSecond']),
                    'Catalog'  => trim($vendor['catalog']),
                    'NotebookUUID'  => $vendor['notebook_uuid'],
                    'CatalogUUID'  => $vendor['catalog_uuid'],
                    'Complects'  => $complects,
                    'Companions'  => $companions,
                    'Collections' => $collections
                ];
            }

            $encoders = array(new XmlEncoder('Elements'));
            $normalizers = array(new ObjectNormalizer());

            $serializer = new Serializer($normalizers, $encoders);

            $xml = $serializer->serialize($return, 'xml');
        } catch (\Exception $ex) {
            var_dump($ex->getMessage());
        }
        echo $xml;
        exit;
    }

    /**
     * @Route("/import-suffixes")
     */
    public function importSuffixAction()
    {
        header('Content-type: text/plain');
        $em = $this->getDoctrine()->getManager();


        set_time_limit(0);

        $url = 'http://www.gallery.kg/api/'
            . urlencode('домосфера') . '/'
            . urlencode('суффиксы-производителей');
        $xmlSuffixes = @simplexml_load_file($url);

        foreach ($xmlSuffixes->Производитель as $xmlSuffix) {
            $suffix = new Entities\Suffix();

            $suffix->setName((string)$xmlSuffix->attributes()->Наименование);
            $suffix->setCode((string)$xmlSuffix->attributes()->Суффикс);

            $em->persist($suffix);
        }

        $em->flush();
        exit;
    }

    private function updateNotebooks($catalogs) {
        $doctrine = $this->getDoctrine();
        foreach($catalogs as $row) {
            if(!$catalog = $doctrine->getRepository('AppBundle:Catalogs')->findOneByUuid($row->Uuid)) {
                $catalog = new Entities\Catalogs();
            }

            $catalog->setUuid($row->Uuid);
            $catalog->setImage($row->ОсновноеИзображение);
            $catalog->setName($row->Наименование);

            $doctrine->getManager()->persist($catalog);
            $doctrine->getManager()->flush();

            $doctrine->getManager()->detach($catalog);
            $doctrine->getManager()->flush();
            $doctrine->getManager()->clear();

            foreach ($row->Тетради->Тетрадь as $ntbk) {
                if (!$notebook = $doctrine->getRepository('AppBundle:Notebooks')->findOneByUuid($ntbk->Uuid)) {
                    $notebook = new Entities\Notebooks();
                }

                $notebook->setUuid($ntbk->Uuid);
                $notebook->setCatalogCode($row->Наименование);
                $notebook->setName($ntbk->Наименование);


                $doctrine->getManager()->persist($notebook);
                $doctrine->getManager()->flush();

                $doctrine->getManager()->detach($notebook);
                $doctrine->getManager()->flush();
                $doctrine->getManager()->clear();

                foreach ($ntbk->Изображения->Изображение as $img) {
                    if (!$image = $doctrine->getRepository('AppBundle:NotebookImage')->findOneByImage($img->Uuid)) {
                        $image = new Entities\NotebookImage();
                    }

                    $image->setNotebook($ntbk->Uuid);
                    $image->setImage($img->Uuid);
                    $image->setWidth($img->Ширина);
                    $image->setHeight($img->Высота);


                    $doctrine->getManager()->persist($image);
                    $doctrine->getManager()->flush();

                    $doctrine->getManager()->detach($image);
                    $doctrine->getManager()->flush();
                    $doctrine->getManager()->clear();
                }
            }
        }

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
            $shop->setCountry($row->Страна);
            $shop->setCity($row->Регион);

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
//            if(!$wallpaper = $doctrine->getRepository('AppBundle:Wallpaper')->findOneBy([
//                'uuid' => $row->Uuid,
//                'shop' => $row->Магазин
//            ])) {
            $wallpaper = new Entities\Wallpaper();
//            }

            $wallpaper->setUuid($row->Uuid);
            $wallpaper->setVendorCode($row->Артикул);

            $wallpaper->setImage($row->Изображение);

            $wallpaper->setSeamlessStructure($row->БесшовнаяСтруктура);
            $wallpaper->setPriceOld($row->ЦенаДоСкидки);
            $wallpaper->setPrice($row->Цена);
            $wallpaper->setCatalog($row->Каталог);
            $wallpaper->setNotebook($row->Тетрадь);
            $wallpaper->setMainNomenclature($row->ВидНоменклатуры);
            $wallpaper->setNomenclature($row->ВидНоменклатурыОригинальный);
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
                switch($filter->Свойство) {
                    case 'глиттер':
                        $wallpaper->setGlitter($filter->Значение == 'Да' ? true : false);
                        break;

                    case 'рисунок':
                        $wallpaper->setPicture($filter->Значение);
                        break;

                    case 'стиль':
                        $wallpaper->setStyle($filter->Значение);
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

                    case 'тип-обоев':
                        $wallpaper->setType($filter->Значение);
                        break;

                    case 'размер':
                        $wallpaper->setSize($filter->Значение);
                        break;

                    case 'коллекция':
                        $wallpaper->setCollectionCode($filter->Значение);
                        break;

                    case 'комната':
                        $wallpaper->setGlitter((string)$filter->Значение);
                        break;
                }
            }
            $doctrine->getManager()->persist($wallpaper);
        }
        $doctrine->getManager()->flush();
        $doctrine->getManager()->clear();
    }

    private function updateComplectData($complects) {
//        var_dump($complects);die;
        $doctrine = $this->getDoctrine();
        echo 'total: ' . count($complects->ЭлементКаталога);
        $i = 0;

        $conn = $this
            ->getDoctrine()
            ->getManager()
            ->getConnection();

        $conn->query('START TRANSACTION;');
        $conn->query('TRUNCATE complect_data RESTART IDENTITY;');
        $conn->query('COMMIT;');

        foreach($complects->ЭлементКаталога as $row) {

            if(!$complect = $doctrine->getRepository('AppBundle:ComplectData')->findOneBy([
                'vendorCode' => $row->Артикул
            ])) {
                $complect = new Entities\ComplectData();
            }
            $i++;
            $complect->setUuid($row->Uuid);
            $complect->setVendorCode($row->Наименование);

            $complect->setImage($row->Изображение);

            $complect->setCatalog($row->Каталог);
            $complect->setNotebook($row->Тетрадь);
            $complect->setMainNomenclature($row->ВидНоменклатурыОригинальный);
            $complect->setNomenclature($row->ВидНоменклатуры);
            $complect->setCountry($row->Страна);
            $complect->setManufacturer($row->Производитель);
            $complect->setCatalogUuid($row->КаталогUuid);
            $complect->setNotebookUuid($row->ТетрадьUuid);
            $complect->setComplect($row->КомплектЖена);
            $complect->setCollectionCode($row->Коллекция);

            $companions = [];
            foreach ($row->ТоварыКомплименты->Товар as $product) {
                $companions[] = (string)$product->Номенклатура;
            }

            $complect->setCompanion(!empty($companions) ? json_encode($companions) : null);
//            $complect->setCompanion($companions);

            foreach ($row->СвойстваФильтра->ЭлементСвойств as $filter) {
                switch($filter->Свойство) {
                    case 'глиттер':
                        $complect->setGlitter($filter->Значение == 'Да' ? true : false);
                        break;

                    case 'рисунок':
                        $complect->setPicture($filter->Значение);
                        break;

                    case '7 Тип обоев':
                        $complect->setTexture($filter->Значение);
                        break;

                    case 'основа':
                        $complect->setBasis($filter->Значение);
                        break;

                    case 'цвет-1':
                        $complect->setColor1($filter->Значение);
                        break;

                    case 'цвет-2':
                        $complect->setColor2($filter->Значение);
                        break;

                    case 'цвет-3':
                        $complect->setColor3($filter->Значение);
                        break;

                    case 'размер':
                        $complect->setSize($filter->Значение);
                        break;

                    case '2 Рисунок1':
                        $complect->setPictureFirst($filter->Значение);
                        break;

                    case '2 Рисунок2':
                        $complect->setPictureSecond($filter->Значение);
                        break;

                    case '9 Стиль':
                        $complect->setStyle($filter->Значение);
                        break;
                    default:
                        break;
                }
            }
            $doctrine->getManager()->persist($complect);
        }

        $doctrine->getManager()->flush();
        $doctrine->getManager()->clear();

        $conn->close();

        echo 'imported: ' . $i;
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
        }
        $doctrine->getManager()->flush();
        $doctrine->getManager()->clear();

    }

    private function updateImage($imageCode){
        $imageCode = $imageCode['image'];
        $doctrine = $this->getDoctrine();
        if (!$imageObj = $this->getDoctrine()
            ->getRepository('AppBundle:Images')
            ->findOneBy([
                'imageCode' => $imageCode
            ])
        ) {
            $imageObj = new Entities\Images();
        }
        try {
            $image = base64_encode(file_get_contents('http://www.gallery.kg/image/'. urlencode('кыргызстан'). '/' . $imageCode));
            $hashImageDb = hash('sha1', stream_get_contents($imageObj->getImage()));
            $downloadedHash = hash('sha1', $image);

            if($hashImageDb == $downloadedHash) {
                $doctrine->getManager()->clear();
                return true;
            }

            $imageObj->setImageCode($imageCode);
            $imageObj->setImage($image);
            $imageObj->setDateTime(new \DateTime());

            $doctrine->getManager()->persist($imageObj);
            $doctrine->getManager()->flush();
            $doctrine->getManager()->clear();
        } catch (\Exception $ex) {

        }
    }

    /**
     * @Route("/read-nomenclature")
     */
    public function readNomenclatureAction()
    {
        $nomenclature = 'ВидНоменклатуры_Key eq \'973ec1ce-ae1e-11e5-8a16-e069956845fd\'';
        $read = new ODataClient();
        $data = $read->makeRequest('Catalog_Номенклатура', '$filter='.$nomenclature);

        return new Response($data, 200, ['Content-type' => 'text/plain']);
    }

}
