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
 * Контроллер отвечает за формирования тетрадей и групп к тетрадям.
 * Работает с сущностями ComplectData, Notebook, NotebookGroup.
 * Сохранение происходит в методе saveNotebookAction.
 *
 *
 * @author scouserlfc91@gmail.com
 */
class NotebookController extends Controller
{
    /**
     * @Route("/")
     * @Method({"GET"})
     */
    public function notebookAction()
    {

        $countries = $this
            ->getDoctrine()
            ->getRepository('AppBundle:ComplectData')
            ->createQueryBuilder('w')
            ->select('DISTINCT(w.country)')
            ->orderBy('w.country', 'ASC')
            ->getQuery()
            ->getResult();

        $sql = 'SELECT COUNT (cd.*) - COUNT (c.*) as not_handled FROM notebook_group c
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

        return $this->render('AppBundle:Wallpaper:notebook.html.php', array(
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

        $sql = 'SELECT COUNT (cd.*) - COUNT (c.*) as not_handled FROM notebook_group c
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

        $sql = 'SELECT COUNT (cd.*) - COUNT (c.*) as not_handled FROM notebook_group c
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
     * @Route("/get-notebooks")
     */
    public function getNotebooksAction(Request $request)
    {
        $query = json_decode($request->request->get('query'));

        $catalog = base64_decode($query->catalog);

        $notebooks = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Notebook')
            ->createQueryBuilder('w')
            ->select('DISTINCT(w.name)')
            ->where('w.catalog=:catalog')
            ->setParameter('catalog', $catalog)
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        $sql = 'SELECT COUNT (cd.*) - COUNT (c.*) as not_handled FROM notebook_group c
RIGHT JOIN complect_data cd ON cd.vendor_code = c.vendor_code';


        $doctrine = $this->getDoctrine();

        $conn = $doctrine->getManager()->getConnection();

        $total =[];
        foreach ($notebooks as $notebook) {
            $q = $sql . ' WHERE cd.collection_code = ? AND cd.catalog = ?';

            $stmt = $conn->prepare($q);
            $stmt->execute([$notebook[1], $catalog]);

            $total[] = ['name' =>$notebook[1], 'not_handled' => $stmt->fetchAll()[0]['not_handled']];
        }

        $conn->close();

        return new Response(json_encode(['notebooks' => $total, 'vendors' => $this->getVendors($catalog)]));
    }

    /**
     * @Route("/get-vendors")
     */
    public function getVendorsAction(Request $request)
    {
        $query = json_decode($request->request->get('query'));

        $catalog = base64_decode($query->catalog);

        return new Response(json_encode($this->getVendors($catalog)));
    }

    /**
     * @Route("/get-notebook-vendors")
     */
    public function getNotebookVendorsAction(Request $request)
    {
        $query = json_decode($request->request->get('query'));

        $notebook = base64_decode($query->notebook);

        $handled = $this
                ->getDoctrine()
                ->getRepository('AppBundle:NotebookGroup')
                ->createQueryBuilder('cg')
                ->select('cg.vendorCode')
                ->getQuery()
                ->getResult(Query::HYDRATE_ARRAY);

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
                w.catalog,
                cc.name as complectCode,
                (CASE WHEN w.vendorCode IN (:handled) THEN 1
                 ELSE 0 END
                ) as handled 
                ")
            ->leftJoin(
                'AppBundle:NotebookGroup',
                'c',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'c.vendorCode = w.vendorCode'
            )
            ->leftJoin(
                'AppBundle:Notebook',
                'cc',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'cc.id = IDENTITY(c.companion)'
            )
            ->where('cc.name=:notebook')
            ->setParameter('notebook', $notebook)
            ->setParameter('handled', $handled)
            ->orderBy('pictureMain DESC, w.notebook ASC, w.vendorCode')
            ->getQuery()
            ->getResult();

        return new Response(json_encode(['vendors' => $vendorCodes]));
    }

    /**
     * @Route("/save")
     */
    public function saveNotebookAction(Request $request)
    {
        $query = json_decode($request->request->get('query'));
        $doctrine = $this->getDoctrine();

        $notebook = $query->notebook;
        $catalog = $query->catalog;
        $manufacturer = $query->manufacturer;
        $property = $query->property;
        $propertySecond = $query->propertySecond;
        $newName = $query->newName;
        $vendorCodes = $query->vendors->node;

        $vendor = $doctrine->getRepository('AppBundle:NotebookGroup')->findOneByVendorCode($vendorCodes[0]);

        $companion = !$notebook ? !$vendor ? null : $vendor->getCompanion() : $doctrine->getRepository('AppBundle:Notebook')->findOneByName($notebook);

        $manufacturer = $doctrine->getRepository('AppBundle:Suffix')->findOneByName($manufacturer);

        if ($companion) {
            $companion->setProperty($property);
            $companion->setPropertySecond($propertySecond);

            $doctrine->getManager()->persist($companion);
            $doctrine->getManager()->flush();
        }
        if ($newName != $notebook && !empty($notebook)) {

            $companion->setName($manufacturer->getCode() . '/' . $catalog . '/' . $newName);

            $doctrine->getManager()->persist($companion);
            $doctrine->getManager()->flush();
        }

        $currently = $doctrine->getRepository('AppBundle:NotebookGroup')->findByCompanion($companion);

        foreach ($currently as $current) {
            $doctrine->getManager()->remove($current);
            $doctrine->getManager()->flush();
        }

        
        if(count($vendorCodes) == 0) {
            $doctrine->getManager()->remove($companion);
            $doctrine->getManager()->flush();
        }

        if(!$companion ) {
            $companion = new Entities\Notebook();

            $doctrine->getManager()->persist($companion);

            $companion->setName($manufacturer->getCode() . '/' . $catalog . '/' . $newName);
            $companion->setCatalog($catalog);
            $companion->setProperty($property);
            $companion->setPropertySecond($propertySecond);

            $doctrine->getManager()->persist($companion);
            $doctrine->getManager()->flush();
        }
        foreach ($vendorCodes as $vendor) {
            $notebookGroup = new Entities\NotebookGroup();
            $notebookGroup->setCompanion($companion);
            $notebookGroup->setVendorCode($vendor);

            $doctrine->getManager()->persist($notebookGroup);
            $doctrine->getManager()->flush();
        }

        return new Response(json_encode(['complects' => $this->getVendors($catalog)]));
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

        $sql = 'SELECT COUNT (cd.*) - COUNT (c.*) as not_handled, COUNT (c.*) as handled FROM notebook_group c
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

    private function getComplect($catalog = false)
    {
        $wallpapers = $this->getDoctrine()->getRepository('AppBundle:NotebookGroup')
            ->createQueryBuilder('c')
            ->select(
                'w.picture, 
                w.vendorCode, 
                w.texture, 
                w.image,
                w.notebook,
                w.style,
                cc.name as complectCode,
                cc.property as property,
                cc.propertySecond as propertySecond
                ')
            ->leftJoin(
                'AppBundle:ComplectData',
                'w',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'c.vendorCode = w.vendorCode'
            )->leftJoin(
                'AppBundle:Notebook',
                'cc',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'cc.id = IDENTITY(c.companion)'
            )->where('w.catalog = :catalog')
                ->setParameter('catalog', $catalog)
            ->orderBy('cc.id');

        $wallpapers = $wallpapers
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        $companions = [];

        foreach($wallpapers as $wallpaper) {
            $companions[$wallpaper['complectCode']][] = $wallpaper;
        }

        return ['complects' => $companions];
    }

    private function getVendors($catalog) {

        $handled = $this
            ->getDoctrine()
            ->getRepository('AppBundle:NotebookGroup')
            ->createQueryBuilder('cg')
            ->select('cg.vendorCode')
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

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
                w.style,
                w.catalog,
                cc.name as complectCode,
                (CASE WHEN w.vendorCode IN (:handled) THEN 1
                 ELSE 0 END
                ) as handled 
                ")
            ->leftJoin(
                'AppBundle:NotebookGroup',
                'c',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'c.vendorCode = w.vendorCode'
            )
            ->leftJoin(
                'AppBundle:Notebook',
                'cc',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'cc.id = IDENTITY(c.companion)'
            )
            ->where('w.catalog=:catalog')
            ->setParameter('catalog', $catalog)
            ->setParameter('handled', $handled)
            ->orderBy('pictureMain DESC, w.notebook ASC, w.vendorCode')
            ->getQuery()
            ->getResult();

        return ['vendors' => $vendorCodes, 'complects' => $this->getComplect($catalog)];
    }
}
