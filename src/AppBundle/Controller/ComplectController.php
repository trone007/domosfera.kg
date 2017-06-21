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
 * Контроллер отвечает за формирование комплектов и групп к комплектам.
 * Работает с сущностями ComplectData, Complect, ComplectGroup.
 * Сохранение происходит в методе saveComplectAction.
 * Контроллер по сути наследник WallpaperController.
 *
 *
 * @author scouserlfc91@gmail.com
 */
class ComplectController extends Controller
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

        $sql = 'SELECT COUNT (cd.*) - COUNT (c.*) as not_handled FROM complect c
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

        return $this->render('AppBundle:Wallpaper:complect.html.php', array(
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

        $sql = 'SELECT COUNT (cd.*) - COUNT (c.*) as not_handled FROM complect c
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

        $sql = 'SELECT COUNT (cd.*) - COUNT (c.*) as not_handled FROM complect c
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
     * @Route("/get-collection")
     */
    public function getCollectionsAction(Request $request)
    {
        $query = json_decode($request->request->get('query'));

        $catalog = base64_decode($query->catalog);

        $collections = $this
            ->getDoctrine()
            ->getRepository('AppBundle:ComplectData')
            ->createQueryBuilder('w')
            ->select('DISTINCT(w.collectionCode)')
            ->where('w.catalog=:catalog')
            ->setParameter('catalog', $catalog)
            ->orderBy('w.collectionCode', 'ASC')
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        $sql = 'SELECT COUNT (cd.*) - COUNT (c.*) as not_handled FROM complect c
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

        return new Response(json_encode(['collections' => $total, 'complects' => $this->getComplect(false, $catalog)]));
    }

    /**
     * @Route("/get-vendors")
     */
    public function getVendorCodesAction(Request $request)
    {
        $query = json_decode($request->request->get('query'));

//        $collection = base64_decode($query->collection);
        $catalog = base64_decode($query->catalog);

        $vendorCodes = $this
            ->getDoctrine()
            ->getRepository('AppBundle:ComplectData')
            ->createQueryBuilder('w')
            ->select("
                w.image,
                w.vendorCode,
                w.texture,
                (CASE WHEN w.picture = 'Полоса' THEN 'Полоса'
                 WHEN w.picture != 'Полоса' AND w.picture != 'Без рисунка' AND w.picture != 'Не задано' AND w.picture != '' THEN 'Рисунок' 
                 ELSE 'Без рисунка' END
                ) as pictureMain,
                w.picture,
                w.notebook,
                c.vendorCode as complectCode
                ")
            ->leftJoin(
                'AppBundle:Complect',
                'c',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'c.vendorCode = w.vendorCode'
            )
            ->where('w.catalog=:catalog')
//            ->andWhere('w.catalog=:catalog')
//            ->setParameter('collection', $collection)
            ->setParameter('catalog', $catalog)
            ->orderBy('pictureMain DESC, w.notebook ASC, w.vendorCode')
            ->getQuery()
            ->getResult();

        return new Response(json_encode(['vendors' => $vendorCodes, 'complects' => $this->getComplect(false, $catalog)]));
    }

    /**
     * @Route("/save-complect")
     */
    public function saveComplectAction(Request $request)
    {
        $query = json_decode($request->request->get('query'));
        $doctrine = $this->getDoctrine();

        $catalog = $query->catalog;
        $root = $query->vendors->root;

        $vendorCodes = $query->vendors->node;


        if(!$complect = $doctrine->getRepository('AppBundle:Complect')->findOneByVendorCode($root) ) {
            $complect = new Entities\Complect();

            $doctrine->getManager()->persist($complect);

            $complect->setComplect($catalog. '-' . $complect->getId());
            $complect->setVendorCode($root);

            $doctrine->getManager()->persist($complect);
            $doctrine->getManager()->flush();
        }

        $currently = $doctrine->getRepository('AppBundle:ComplectGroup')->findByComplectCode($complect);

        foreach ($currently as $current) {
            $doctrine->getManager()->remove($current);
        }

        $doctrine->getManager()->flush();

        foreach ($vendorCodes as $vendor) {
            $complectGroup = new Entities\ComplectGroup();
//            $complectGroup->setCollectionCode($collection);
            $complectGroup->setComplectCode($complect);
            $complectGroup->setVendorCode($vendor);

            $doctrine->getManager()->persist($complectGroup);
        }

        $doctrine->getManager()->flush();

        return new Response(json_encode($this->getComplect(false, $catalog)));
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

        $sql = 'SELECT COUNT (cd.*) - COUNT (c.*) as not_handled, COUNT (c.*) as handled FROM complect c
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

    private function getComplect($collectionCode = false, $catalog = false)
    {

        $wallpapers = $this->getDoctrine()->getRepository('AppBundle:ComplectGroup')
            ->createQueryBuilder('c')
            ->select(
                'w.picture, 
                w.vendorCode, 
                w.texture, 
                w.image,
                w.notebook,
                cc.vendorCode as rootVendor
                ')
            ->leftJoin(
                'AppBundle:ComplectData',
                'w',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'c.vendorCode = w.vendorCode'
            )->leftJoin(
                'AppBundle:Complect',
                'cc',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'cc.id = IDENTITY(c.complectCode)'
            );

        $roots = $this->getDoctrine()->getRepository('AppBundle:Complect')
            ->createQueryBuilder('c')
            ->select(
                '
                cd.picture, 
                cd.vendorCode, 
                cd.texture, 
                cd.image,
                cd.notebook,
                cd.catalog
                ')
            ->leftJoin(
                'AppBundle:ComplectData',
                'cd',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'cd.vendorCode = c.vendorCode'
            );

        $wallpapers->where('w.catalog = :catalog')
            ->setParameter('catalog', $catalog);

        $roots->where('cd.catalog = :catalog')
            ->setParameter('catalog', $catalog);

        $wallpapers = $wallpapers
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        $roots = $roots->getQuery()->getResult(Query::HYDRATE_ARRAY);

        return ['complects' => $wallpapers, 'roots' => $roots];
    }
}
