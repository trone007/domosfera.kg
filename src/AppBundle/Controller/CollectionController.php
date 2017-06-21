<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity as Entities;
use AppBundle\Image as Image;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * Контроллер отвечает за формирования коллекций и групп к коллекциям.
 * Работает с сущностями ComplectData, Collection, CollectionGroup.
 * Один артикул - может быть в нескольких коллекциях.
 * Сохранение происходит в методе saveCollectionAction.
 * Наименование коллекции отлично от наименования коллекций в 1С.
 * Контроллер по сути наследник NotebookController.
 *
 *
 * @author scouserlfc91@gmail.com
 */
class CollectionController extends Controller
{
    /**
     * @Route("/")
     * @Method({"GET"})
     */
    public function collectionAction()
    {

        $countries = $this
            ->getDoctrine()
            ->getRepository('AppBundle:ComplectData')
            ->createQueryBuilder('w')
            ->select('DISTINCT(w.country)')
            ->orderBy('w.country', 'ASC')
            ->getQuery()
            ->getResult();

        $sql = 'SELECT COUNT (cd.*) - COUNT (c.*) as not_handled FROM collection_group c
RIGHT JOIN complect_data cd ON cd.vendor_code = c.vendor_code';


        $doctrine = $this->getDoctrine();

        $conn = $doctrine->getManager()->getConnection();

        $total =[];
        foreach ($countries as $country) {
            $q = $sql . ' WHERE cd.country = ?';

            $stmt = $conn->prepare($q);
            $stmt->execute([$country]);

            $total[] = ['name' =>$country, 'not_handled' => $stmt->fetchAll()[0]['not_handled']];
        }

        $conn->close();

        return $this->render('AppBundle:Wallpaper:collectionMade.html.php', array(
            'countries' => $total
        ));
    }

    /**
     * @Route("/get-manufacturer")
     */
    public function getManufacturerAction(Request $request)
    {
        $query = json_decode($request->request->get('query'));

        $country = base64_decode($query->country);


        $manufacturers = $this
            ->getDoctrine()
            ->getRepository('AppBundle:ComplectData')
            ->createQueryBuilder('w')
            ->select('DISTINCT(w.manufacturer)')
            ->where('w.country=:country')
            ->setParameter('country', $country)
            ->orderBy('w.manufacturer', 'ASC')
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        $sql = 'SELECT COUNT (cd.*) - COUNT (c.*) as not_handled FROM collection_group c
RIGHT JOIN complect_data cd ON cd.vendor_code = c.vendor_code';


        $doctrine = $this->getDoctrine();

        $conn = $doctrine->getManager()->getConnection();

        $total =[];
        foreach ($manufacturers as $manufacturer) {
            $q = $sql . ' WHERE cd.country = ? AND cd.manufacturer = ?';

            $stmt = $conn->prepare($q);
            $stmt->execute([$country, $manufacturer[1]]);

            $total[] = ['name' =>$manufacturer[1], 'not_handled' => $stmt->fetchAll()[0]['not_handled']];
        }

        $conn->close();

        return new Response(json_encode($total));
    }

    /**
     * @Route("/get-catalog")
     */
    public function getCatalogAction(Request $request)
    {
        $query = json_decode($request->request->get('query'));

        $manufacturer = base64_decode($query->manufacturer);

        $catalogs = $this
            ->getDoctrine()
            ->getRepository('AppBundle:ComplectData')
            ->createQueryBuilder('w')
            ->select('DISTINCT(w.catalog)')
            ->where('w.manufacturer=:manufacturer')
            ->setParameter('manufacturer', $manufacturer)
            ->orderBy('w.catalog', 'ASC')
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        $sql = 'SELECT COUNT (cd.*) - COUNT (c.*) as not_handled FROM collection_group c
RIGHT JOIN complect_data cd ON cd.vendor_code = c.vendor_code';


        $doctrine = $this->getDoctrine();

        $conn = $doctrine->getManager()->getConnection();

        $total =[];
        foreach ($catalogs as $catalog) {
            $q = $sql . ' WHERE cd.manufacturer = ? AND cd.catalog = ?';

            $stmt = $conn->prepare($q);
            $stmt->execute([$manufacturer, $catalog[1]]);

            $total[] = ['name' =>$catalog[1], 'not_handled' => $stmt->fetchAll()[0]['not_handled']];
        }

        $conn->close();

        return new Response(json_encode($total));
    }

    /**
     * @Route("/get-collections")
     */
    public function getCollectionsAction(Request $request)
    {
        $query = json_decode($request->request->get('query'));

        $catalog = base64_decode($query->catalog);

        $collections = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Collection')
            ->createQueryBuilder('w')
            ->select('DISTINCT(w.name)')
            ->where('w.catalog=:catalog')
            ->setParameter('catalog', $catalog)
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        $sql = 'SELECT COUNT (cd.*) - COUNT (c.*) as not_handled FROM collection_group c
RIGHT JOIN complect_data cd ON cd.vendor_code = c.vendor_code';


        $doctrine = $this->getDoctrine();

        $conn = $doctrine->getManager()->getConnection();

        $total =[];
        foreach ($collections as $collection) {
            $q = $sql . ' WHERE cd.collection_code = ? AND cd.catalog = ?';

            $stmt = $conn->prepare($q);
            $stmt->execute([$collection[1], $catalog]);

            $total[] = ['name' =>$collection[1], 'not_handled' => $stmt->fetchAll()[0]['not_handled']];
        }

        $conn->close();

        return new Response(json_encode(['collections' => $total, 'vendors' => $this->getVendorCodes($catalog)]));
    }

    /**
     * @Route("/get-vendors")
     */
    public function getVendorCodesAction(Request $request)
    {
        $query = json_decode($request->request->get('query'));

        $catalog = base64_decode($query->catalog);
        $manufacturer = base64_decode($query->manufacturer);

        return new Response(json_encode($this->getVendorCodes($catalog, $manufacturer)));
    }

    /**
     * @Route("/get-collection-vendors")
     */
    public function getCollectionVendorsAction(Request $request)
    {
        $query = json_decode($request->request->get('query'));

        $notebook = base64_decode($query->collection);

        $vendorCodes = $this
            ->getDoctrine()
            ->getRepository('AppBundle:ComplectData')
            ->createQueryBuilder('w')
            ->select("
                w.image,
                w.vendorCode,
                w.picture,
                w.notebook,
                w.catalog,
                cc.name as complectCode
                ")
            ->leftJoin(
                'AppBundle:CollectionGroup',
                'cg',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'w.vendorCode = cg.vendorCode'
            )
            ->leftJoin(
                'AppBundle:Collection',
                'cc',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'IDENTITY(cg.companion) = cc.id'
            )
            ->where('cc.name=:notebook')
            ->setParameter('notebook', $notebook)
            ->orderBy('w.vendorCode')
            ->getQuery()
            ->getResult();

        return new Response(json_encode(['vendors' => $vendorCodes]));
    }
    /**
     * @Route("/save")
     */
    public function saveCollectionAction(Request $request)
    {
        $query = json_decode($request->request->get('query'));
        $doctrine = $this->getDoctrine();

        $collectionMain = $query->collection;
        $catalog = $query->catalog;
        $manufacturer = $query->manufacturer;
        $newName = $query->newName;
        $collectionType = $query->collectionType;

        $vendorCodes = $query->vendors->node;

        if($collectionMain && count($vendorCodes) == 0) {
            $companion = $doctrine->getRepository('AppBundle:Collection')->findOneByName($collectionMain);

            $currently = $doctrine->getRepository('AppBundle:CollectionGroup')->findByCompanion($companion);

            foreach ($currently as $current) {
                $doctrine->getManager()->remove($current);
            }

            $doctrine->getManager()->remove($companion);
            $doctrine->getManager()->flush();

            return new Response(json_encode(['complects' => $this->getVendorCodes($catalog, $manufacturer)]));
        }

        if ($collectionMain) {
            $companion = $doctrine->getRepository('AppBundle:Collection')->findOneByName($collectionMain);
            $companion->setProperty($collectionType);
            $doctrine->getManager()->persist($companion);
            $doctrine->getManager()->flush();
        }

        $groups = $doctrine
            ->getRepository('AppBundle:CollectionGroup')
            ->createQueryBuilder('cg')
            ->select('DISTINCT(cc.id)')
            ->leftJoin(
                'AppBundle:Collection',
                'cc',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'IDENTITY(cg.companion) = cc.id'
            )
            ->where('cg.vendorCode = :vendor')
            ->setParameter('vendor',$vendorCodes[0])
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        $collections = $doctrine
            ->getRepository('AppBundle:CollectionGroup')
            ->createQueryBuilder('cg')
            ->select('cc.id as collection, cg.vendorCode')
            ->leftJoin(
                'AppBundle:Collection',
                'cc',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'IDENTITY(cg.companion) = cc.id'
            )
            ->where('cc.id IN (:groups)')
            ->setParameter('groups', $groups)
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        $colls = [];

        foreach($collections as $collection) {
            $colls[$collection['collection']][] = $collection['vendorCode'];
        }

        foreach($colls as $col) {
            sort($col);
            sort($vendorCodes);

            if(count($col) != count($vendorCodes)) continue;

            if($col == $vendorCodes) {
                $companion = $doctrine->getRepository('AppBundle:Collection')->findOneByName($collectionMain);

                if($collectionMain && count($vendorCodes) == 0) {

                    $currently = $doctrine->getRepository('AppBundle:CollectionGroup')->findByCompanion($companion);

                    foreach ($currently as $current) {
                        $doctrine->getManager()->remove($current);
                    }
                    $doctrine->getManager()->flush();

                    if(count($vendorCodes) == 0) {
                        $doctrine->getManager()->remove($companion);
                        $doctrine->getManager()->flush();
                    }
                }

                if ($companion && $newName != $collectionMain) {
                    $manufacturer = $doctrine->getRepository('AppBundle:Suffix')->findOneByName($manufacturer);
                    $name =
                        $manufacturer->getCode() .
                        '/' .
                        $catalog . '/' .
                        $newName;
                    $companion->setName($name);
                    $doctrine->getManager()->persist($companion);
                    $doctrine->getManager()->flush();
                }
                return new Response(json_encode(['complects' => $this->getVendorCodes($catalog, $manufacturer)]));
            }
        }


        if($collectionMain) {
            $companion = $doctrine->getRepository('AppBundle:Collection')->findOneByName($collectionMain);

            $currently = $doctrine->getRepository('AppBundle:CollectionGroup')->findByCompanion($companion);

            foreach ($currently as $current) {
                $doctrine->getManager()->remove($current);
                $doctrine->getManager()->flush();
            }


            if(count($vendorCodes) == 0) {
                $doctrine->getManager()->remove($companion);
                $doctrine->getManager()->flush();
            }
        }
        $catalogs = $doctrine->getRepository('AppBundle:Collection')->findByCatalog($catalog);
        $manufacturer = $doctrine->getRepository('AppBundle:Suffix')->findOneByName($manufacturer);

        foreach ($catalogs as $cat) {
            $collection = explode('/', $cat->getName());

            if(isset($collection[2]) && strlen($collection[0]) < 4) {
                if((int)($collection[2]) == 0) {
                    $count[] = $collection[3];

                } else {
                    $count[] = $collection[2];
                }
            }
        }
//
//        sort($count);
//        $cnt = count($count);
//        for ($i = 0; $i < $cnt; $i++) {
//            if($count[0] != 1){
//                $count = 1;
//                break;
//            }
//
//            if(isset($count[$i+1]) && ($count[$i+1] - $count[$i]) > 1) {
//                $count = $count[$i]+1;
//                break;
//            }
//        }

        $name =
            $manufacturer->getCode() .
            '/' .
            $catalog . '/' .
            $newName;


        if(!$companion) {
            $companion = new Entities\Collection();

            $doctrine->getManager()->persist($companion);

            $companion->setName($name);
            $companion->setCatalog($catalog);
            $companion->setProperty($collectionType);

            $doctrine->getManager()->persist($companion);
            $doctrine->getManager()->flush();
        }

        foreach ($vendorCodes as $vendor) {
            $collectionComplect = new Entities\CollectionGroup();
            $collectionComplect->setCompanion($companion);
            $collectionComplect->setVendorCode($vendor);

            $doctrine->getManager()->persist($collectionComplect);
            $doctrine->getManager()->flush();
        }

        return new Response(json_encode(['complects' => $this->getVendorCodes($catalog, $manufacturer)]));
    }


    /**
     * @Route("/get-counts")
     */
    public function getCounters(Request $request)
    {
        $query = json_decode($request->get('query'));
        $country = base64_decode($query->country);
        $manufacturer = base64_decode($query->manufacturer);
        $catalog = base64_decode($query->catalog);
        $collection = base64_decode($query->collection);

        $total = [];

        $doctrine = $this->getDoctrine();

        $conn = $doctrine->getManager()->getConnection();

        $sql = 'SELECT COUNT (cd.*) - COUNT (c.*) as not_handled, COUNT (c.*) as handled FROM collection_group c
RIGHT JOIN complect_data cd ON cd.vendor_code = c.vendor_code';

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $total['total'] = $stmt->fetchAll();

        if($country) {
            $q = $sql . ' WHERE cd.country = ?';

            $stmt = $conn->prepare($q);
            $stmt->execute([$country]);
            $total['country'] = $stmt->fetchAll();
        }

        if($manufacturer) {
            $q = $sql . ' WHERE cd.manufacturer = ?';

            $stmt = $conn->prepare($q);
            $stmt->execute([$manufacturer]);
            $total['manufacturer'] = $stmt->fetchAll();
        }

        if($catalog) {
            $q = $sql . ' WHERE cd.catalog = ?';

            $stmt = $conn->prepare($q);
            $stmt->execute([$catalog]);
            $total['catalog'] = $stmt->fetchAll();
        }

        if($collection) {
            $q = $sql . ' WHERE cd.collection_code = ? AND cd.catalog = ?';

            $stmt = $conn->prepare($q);
            $stmt->execute([$collection, $catalog]);
            $total['collection'] = $stmt->fetchAll();
        }


        $conn->close();

        return new Response(json_encode($total));
    }


    /**
     * @Route("/get-companion")
     */
    public function getCompanionByVendorCode(Request $request)
    {
        $query = json_decode($request->get('query'));
        $vendorCode = base64_decode($query->vendorCode);

        $companion = $this->getDoctrine()->getRepository('AppBundle:Companion')->findOneByVendorCode($vendorCode);

        $companionGroup = $this->getDoctrine()
            ->getRepository('AppBundle:CompanionGroup')
            ->createQueryBuilder('cg')
            ->select('cg.vendorCode')
            ->where('cg.companionCode = :companion')
            ->setParameter('companion', $companion)
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);
//        if(!$companionGroup) {
//            $companions = $this->getDoctrine()
//                ->getRepository('AppBundle:CompanionGroup')
//                ->createQueryBuilder('cg')
//                ->select(
//                    'IDENTITY(cg.companionCode) id,
//                    cc.vendorCode as rootVendor
//                ')
//                ->leftJoin('cg.companionCode', 'cc')
//                ->where('cg.vendorCode = :vendorCode')
//                ->setParameter('vendorCode', $vendorCode)
//                ->getQuery()
//                ->getResult(Query::HYDRATE_ARRAY);
//        } else {
//            $companions = $this->getDoctrine()
//                ->getRepository('AppBundle:Companion')
//                ->createQueryBuilder('c')
//                ->select('c.id')
//                ->where('c.vendorCode IN (:vendorCodes)')
//                ->setParameter('vendorCodes', $companionGroup)
//                ->getQuery()
//                ->getResult(Query::HYDRATE_ARRAY);
//        }
//
//        $companionsArray = [$companion->getId()];
//
//        foreach ($companions as $companion) {
//            $companionsArray[] = (int)$companion['id'];
//        }
//
//        $companionGroup = $this->getDoctrine()
//            ->getRepository('AppBundle:CompanionGroup')
//            ->createQueryBuilder('cg')
//            ->select('DISTINCT(cg.vendorCode) as vendorCode')
//            ->where('cg.companionCode IN (:companion)')
//            ->setParameter('companion', $companionsArray)
//            ->getQuery()
//            ->getResult(Query::HYDRATE_ARRAY);
//
//        if(is_array($companion) && isset($companion['rootVendor'])) {
//            array_unshift($companionGroup, ['vendorCode' => $companion['rootVendor']]);
//        }

        return new Response(json_encode(['group' => $companionGroup]));
    }

    /**
     * @Route("/change-name")
     * @Method("POST")
     */
    public function changeCollectionName(Request $request)
    {
        $query = json_decode($request->get('query'));
        $oldName = base64_decode($query->oldName);
        $newName = base64_decode($query->newName);

        $collection = $this->getDoctrine()->getRepository('AppBundle:Collection')->findOneByName($oldName);
        $nameExists = $this->getDoctrine()->getRepository('AppBundle:Collection')->findOneByName($newName);

        if($nameExists) {
            throw new \Exception("Error");
        }

        $collection->setName($newName);

        $this->getDoctrine()->getManager()->persist($collection);
        $this->getDoctrine()->getManager()->flush();
        return new Response(json_encode(['success' => true]));
    }

    private function getComplect($catalog = false, $manufacturer = false)
    {
        $wallpapers = $this->getDoctrine()->getRepository('AppBundle:CollectionGroup')
            ->createQueryBuilder('c')
            ->select(
                'w.picture, 
                w.vendorCode, 
                w.texture, 
                w.image,
                w.notebook,
                cc.name as complectCode,
                cc.property as property
                ')
            ->leftJoin(
                'AppBundle:ComplectData',
                'w',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'c.vendorCode = w.vendorCode'
            )->leftJoin(
                'AppBundle:Collection',
                'cc',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'cc.id = IDENTITY(c.companion)'
            )->where('w.catalog = :catalog')
            ->setParameter('catalog', $catalog)
            ->orderBy('cc.id');

        if ($manufacturer) {
            $wallpapers->andWhere('w.manufacturer = :manufacturer')
                ->setParameter('manufacturer', $manufacturer);
        }

        $wallpapers = $wallpapers
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        $companions = [];

        foreach($wallpapers as $wallpaper) {
            $companions[$wallpaper['complectCode']][] = $wallpaper;
        }

        return ['complects' => $companions];
    }

    private function getVendorCodes($catalog, $manufacturer) {

        $doctrine = $this->getDoctrine();

        $conn = $doctrine->getManager()->getConnection();

        $sql = 'SELECT
                w.image,
                w.vendor_code as "vendorCode",
                w.texture,
                (CASE WHEN w.picture = \'Полоса\' THEN \'Полоса\'
                 WHEN w.picture != \'Полоса\' AND w.picture != \'Без рисунка\' AND w.picture != \'Не задано\' AND w.picture != \'\' THEN \'Рисунок\'
                 ELSE \'Без рисунка\' END
                ) as pictureMain,
                w.picture,
                cc.collection as "complectCode",
                nn.notebook as "notebook",
                w.catalog
                FROM complect_data w
                LEFT JOIN (
                  SELECT array_agg(c.name) as collection, cg.vendor_code FROM collection c
                  INNER JOIN collection_group cg ON  cg.companion_id = c.id
                  GROUP BY cg.vendor_code
                  ) cc ON cc.vendor_code = w.vendor_code
                LEFT JOIN (
                  SELECT n.name as notebook, ng.vendor_code FROM notebook n
                  INNER JOIN notebook_group ng ON  ng.companion_id = n.id
                  ) nn ON nn.vendor_code = w.vendor_code
                WHERE w.catalog = ?
                ORDER BY w.vendor_code';

        $stmt = $conn->prepare($sql);
        $stmt->execute([$catalog]);
        $vendorCodes = $stmt->fetchAll();

        return ['vendors' => $vendorCodes, 'complects' => $this->getComplect($catalog, $manufacturer)];
    }
}
