<?php
namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity as Entities;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMapping;
//class Symfony\Component\Security\AuthenticationAuthenticationUtils;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

/**
 * Тут производится работа с остальным сайтом.
 * в методе нью обновляется сессия(планируется пренос на страницу авторизации).
 * методы логин и логин чек являются псевдометодами.
 *
 * @author scouserlfc91@gmail.com
 */

class DefaultController extends Controller
{
    /**
     * @Route("/new/")
     * @Route("/other-new/")
     */
    public function indexAction()
    {
        $this->updateSession();
        return $this->render('AppBundle:Default:index.html.php', array(
        ));
    }

    /**
     * @Route("/change-region/{region}")
     */
    public function changeRegionAction($region)
    {
        $this->get('session')->set('region', $region);

        return $this->redirect($_SERVER["HTTP_REFERER"]);
    }

    /**
     * @Route("/login_check")
     */
    public function loginCheckAction()
    {

    }
    /**
     * @Route("/logout")
     */
    public function logoutAction()
    {

    }

    /**
     * @Route("/shaping/{page}")
     */
    public function shapingAction($page)
    {
        $countries = $this
            ->getDoctrine()
            ->getRepository('AppBundle:ComplectData')
            ->createQueryBuilder('w')
            ->select('DISTINCT(w.country)')
            ->orderBy('w.country', 'ASC')
            ->getQuery()
            ->getResult();

        switch ($page) {
            case 1:
                $table = 'companion';
                $field = 'c.vendor_code';
                break;
            case 2:
                $table = 'complect';
                $field = 'c.vendor_code';
                break;
            case 3:
                $table = 'notebook_group';
                $field = 'c.vendor_code';
                break;
            case 4:
                $table = 'collection_group';
                $field = 'c.vendor_code';
                break;
        }


        $sql = 'SELECT COUNT (cd.*) - COUNT (c.*) as not_handled FROM ' . $table  .' c
RIGHT JOIN complect_data cd ON cd.vendor_code = ' . $field;


        $doctrine = $this->getDoctrine();

        $conn = $doctrine->getManager()->getConnection();

        $total =[];
        foreach ($countries as $country) {
            $q = $sql . ' WHERE cd.country = ?';

            $stmt = $conn->prepare($q);
            $stmt->execute([$country[1]]);

            $total[] = ['name' =>$country, 'not_handled' => $stmt->fetchAll()[0]['not_handled']];
        }

        $conn->close();

        switch ($page) {
            case 1:
                return $this->render('AppBundle:Wallpaper:companion.html.php', array(
                    'countries' => $total
                ));
                break;
            case 2:
                return $this->render('AppBundle:Wallpaper:complect.html.php', array(
                    'countries' => $total
                ));
                break;
            case 3:
                return $this->render('AppBundle:Wallpaper:notebook.html.php', array(
                    'countries' => $total
                ));
                break;
            case 4:
                return $this->render('AppBundle:Wallpaper:collectionMade.html.php', array(
                    'countries' => $total
                ));
                break;
        }
    }

    /**
     * @Route("/collections")
     * @Route("/new/collections")
     * @Route("/other-new/collections")
     */
    public function catalogAction(Request $request)
    {
        try{
            $query = $this->getDoctrine()
            ->getRepository('AppBundle:Wallpaper')
            ->createQueryBuilder('w')
            ->select('MAX(w.price) AS max_price, MIN(w.price) AS min_price, MAX( ROUND(w.price * w.marketPlan, 2) )as max_m_price, MIN( ROUND(w.price * w.marketPlan, 2) )as min_m_price')
            ->where('w.shop = :shop')
            ->andWhere('w.nomenclature = :nomenclature')
            ->setParameter('shop', 'kgb')
            ->setParameter('nomenclature', 'Обои')
            ->setMaxResults(1);

            $maxMin = $query->getQuery()->getResult()[0];
        } catch (\Exception $ex) {
            echo $ex->getMessage();
        }
        return $this->render('AppBundle:Wallpaper:catalogs.html.php', array(
            'maxMin'    => $maxMin
        ));
    }

    /**
     * @Route("/get-filtered-collections")
     */
    public function getCollectionsAction(Request $request)
    {
        $query = json_decode($request->request->get('query'));
        $priceStart = isset($query->priceStart) ? $query->priceStart : null;
        $priceFinish = isset($query->priceFinish) ? $query->priceFinish : null;

        $priceType = isset($query->priceType) ? $query->priceType : null;


        $vendor = isset($query->vendor) ? $query->vendor : null;
        $orderBy = isset($query->orderBy) ? $query->orderBy : null;

        $colors = isset($query->colors) ? $query->colors : null;
        $pictures = isset($query->pictures) ? $query->pictures : null;
        $textures = isset($query->textures) ? $query->textures : null;
        $sizes = isset($query->sizes) ? $query->sizes : null;
        $countries = isset($query->countries) ? $query->countries : null;
        $halyava = isset($query->halyava) ? $query->halyava : null;
        $hot = isset($query->hot) ? $query->hot : null;
        $new = isset($query->new) ? $query->new : null;

        $glitter = isset($query->glitter) ? $query->glitter : null;


        $catalogsTmp = $this
            ->getDoctrine()
            ->getRepository('AppBundle:ComplectData')
            ->createQueryBuilder('cd')
            ->select('DISTINCT(cd.collectionCode)')
            ->leftJoin(
                'AppBundle:Wallpaper',
                'w',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'w.vendorCode = cd.vendorCode'
            )
            ->orderBy('cd.collectionCode', 'ASC')
            ->where('w.shop = :shop')
            ->setParameter('shop',  $this->get('session')->get('shop'))
            ->orderBy('cd.collectionCode', 'ASC');

        if($priceStart) {
            if ($priceType) {
                $catalogsTmp->andWhere('ROUND(w.price*w.marketPlan, 2) >= :priceStart AND ROUND(w.price*w.marketPlan, 2) - 1 <= :priceFinish');
            } else {
                $catalogsTmp->andWhere('w.price BETWEEN :priceStart AND :priceFinish');
            }
            $catalogsTmp
                ->setParameter('priceStart', $priceStart)
                ->setParameter('priceFinish', $priceFinish+1);
        }

        if ($vendor) {
            $catalogsTmp->andWhere("w.vendorCode like :vendor")
                ->setParameter('vendor', "%" . $vendor . "%");
        }

        if ($colors) {
            $i = 0;

            $queryString = '';
            foreach ($colors as $color) {
                $queryString .= 'w.color1 = :color' . $i . ' OR w.color2 = :color' . $i . ' OR w.color3 = :color' . $i;

                if ($i < count($colors) - 1) {
                    $queryString .= ' OR ';
                }

                $catalogsTmp->setParameter('color' . $i++, $color);
            }

            $catalogsTmp->andWhere($queryString);
        }

        if ($pictures) {
            $i = 0;

            $queryString = '';
            foreach ($pictures as $picture) {
                $queryString .= 'w.picture = :picture' . $i;

                if ($i < count($pictures) - 1) {
                    $queryString .= ' OR ';
                }

                $catalogsTmp->setParameter('picture' . $i++, $picture);
            }

            $catalogsTmp->andWhere($queryString);
        }

        if ($sizes) {
            $i = 0;
            $queryString = '';
            foreach ($sizes as $size) {
                $queryString .= 'w.size = :size' . $i;

                if ($i < count($sizes) - 1) {
                    $queryString .= ' OR ';
                }
                $catalogsTmp->setParameter('size' . $i++, round($size, 2));
            }

            $catalogsTmp->andWhere($queryString);
        }

        if ($countries) {
            $i = 0;

            $queryString = '';
            foreach ($countries as $country) {
                $queryString .= 'w.country = :country' . $i;

                if ($i < count($countries) - 1) {
                    $queryString .= ' OR ';
                }

                $catalogsTmp->setParameter('country' . $i++, $country);
            }

            $catalogsTmp->andWhere($queryString);
        }

        if ($textures) {
            $i = 0;

            $queryString = '';
            foreach ($textures as $texture) {
                $queryString .= 'w.texture = :texture' . $i;

                if ($i < count($textures) - 1) {
                    $queryString .= ' OR ';
                }

                $catalogsTmp->setParameter('texture' . $i++, $texture);
            }

            $catalogsTmp->andWhere($queryString);
        }

        if ($glitter != "") {
            if ($glitter == "null") {
                $catalogsTmp->andWhere('w.glitter is NULL');
            } else {
                $catalogsTmp->andWhere('w.glitter = :glitter')
                    ->setParameter('glitter', $glitter);
            }
        }


        if ($halyava) {
            $catalogsTmp->andWhere('w.points = 1 OR w.points = 2');
//            $catalogsTmp->orderBy('halyava', 'DESC');
        }
        if ($hot) {
            $catalogsTmp->andWhere('w.points = 5 OR w.points = 4');
//            $catalogsTmp->orderBy('w.successfull', 'DESC');
        }
        if ($new) {
            $catalogsTmp->andWhere('w.points = 7');
//            $catalogsTmp->orderBy('w.dateTime', 'DESC');
        }

        $catalogsTmp = $catalogsTmp
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        $vendors = $this
            ->getDoctrine()
            ->getRepository('AppBundle:ComplectData')
            ->createQueryBuilder('cd')
            ->select(' 
                 w.shop, 
                w.vendorCode, 
                w.manufacturer, 
                w.speed,
                w.points,
                w.successfull, 
                w.dateTime, 
                w.image, 
                w.country, 
                COALESCE(w.size , 0.53) as size, 
                w.catalog,
                w.id, 
                w.uuid,
                w.price,
                w.priceOld,
                w.glitter,
                w.currency,
                ROUND (wc.count/w.marketPlan , 2)  as m_count,
                ROUND (wc.totalCount/w.marketPlan , 2)  as m_totalCount,
                (w.priceOld - w.price) as halyava,
                ROUND (w.priceOld*w.marketPlan , 2)  as m_old_price,
                ROUND (w.price*w.marketPlan , 2)  as m_price,
                ROUND (1/w.marketPlan/COALESCE(nullif(w.size, 0) , 0.53), 2)  as height,
                wc.count,
                wc.totalCount,
                cd.collectionCode
                ')
            ->leftJoin(
                'AppBundle:Wallpaper',
                'w',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'w.vendorCode = cd.vendorCode'
            )
            ->leftJoin(
                'AppBundle:WallpaperCount',
                'wc',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'wc.wallpaperUuid = w.uuid AND w.shop = wc.shop'
            )
            ->where('cd.collectionCode IN (:collectionCode)')
            ->andWhere('w.shop = :shop')
            ->setParameter('shop',  $this->get('session')->get('shop'))
            ->setParameter('collectionCode', $catalogsTmp)
            ->orderBy('cd.collectionCode', 'ASC');

        if ($halyava) {
            $vendors->orderBy('halyava', 'DESC');
        }
        if ($hot) {
            $vendors->orderBy('w.successfull', 'DESC');
        }
        if ($new) {
            $vendors->orderBy('w.dateTime', 'DESC');
        }
        if(!empty($orderBy) && count((array)$orderBy) > 0) {
            $vendors->orderBy('w.' . $orderBy->column, $orderBy->type);
            if($orderBy->column == 'price') {
                if($priceType) {
                    $vendors->orderBy('ROUND(w.price*w.marketPlan, 2)', $orderBy->type);
                }
            }
        }
        $vendors = $vendors->getQuery()
        ->getResult(Query::HYDRATE_ARRAY);

        $count = count($vendors);

        $catalogs = [];

        foreach ($vendors as $vendor) {

            $catalogs[$vendor['collectionCode']]['vendors'][] = $vendor;
            $catalogs[$vendor['collectionCode']]['country'] = $vendor['country'];
            $catalogs[$vendor['collectionCode']]['manufacturer'] = $vendor['manufacturer'];
            $catalogs[$vendor['collectionCode']]['catalog'] = $vendor['catalog'];
        }

        return new Response(json_encode(['catalogs' => $catalogs, 'count' => $count]), 200, ['Content-type: application/json']);
    }

    /**
     * @Route("/get-interior-image/{catalog}")
     */
    public function getInteriorImage($catalog)
    {
        $image = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Catalogs')
            ->createQueryBuilder('c')
            ->select('c.image')
            ->where('c.name = :name')
            ->andWhere('c.image IS NOT NULL')
            ->setParameter('name', $catalog)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        if (empty($image[0]['image'])) {
            $image = $this
                ->getDoctrine()
                ->getRepository('AppBundle:NotebookImage')
                ->createQueryBuilder('ni')
                ->select('ni.image')
                ->leftJoin(
                    'AppBundle:Notebooks',
                    'n',
                    \Doctrine\ORM\Query\Expr\Join::WITH,
                    'n.uuid = ni.notebook'
                )
                ->where('n.catalogCode = :catalog')
                ->andWhere('ni.image IS NOT NULL')
                ->setParameter('catalog', $catalog)
                ->setMaxResults(1)
                ->getQuery()
                ->getResult(Query::HYDRATE_ARRAY);
        }

        if (empty($image[0]['image'])) {
            $image = $this
                ->getDoctrine()
                ->getRepository('AppBundle:Wallpaper')
                ->createQueryBuilder('ni')
                ->select('ni.image')
                ->where('ni.catalog = :catalog')
                ->andWhere('ni.image IS NOT NULL')
                ->setParameter('catalog', $catalog)
                ->setMaxResults(1)
                ->getQuery()
                ->getResult(Query::HYDRATE_ARRAY);

        }
        $url = "http://gallery.kg/image/". urlencode("кыргызстан"). "/" . (!empty($image[0]['image']) ?
            $image[0]['image'] : "00000000-0000-0000-0000-000000000000");

        return new Response(
            file_get_contents($url),
            200,
            ['Content-type' => 'image/jpeg']
        );
    }

    /**
     * @Route("/new/shops")
     */
    public function shopsAction()
    {
        $region = $this->get('session')->get('region');

        $shop = $this->getDoctrine()->getRepository('AppBundle:Shop')->findOneByCity($region);
        if(!$shop) {
            $shop = $this->getDoctrine()->getRepository('AppBundle:Shop')->findOneByCity('бишкек');
        }
        return $this->render('AppBundle:Default:shops.html.php', array(
            'shop' => $shop,
            'region' => $region
        ));
    }

    /**
     * @Route("/compare")
     * @Route("/new/compare")
     * @Route("/other-new/compare")
     */
    public function compareAction()
    {

        return $this->render('AppBundle:Wallpaper:compare.html.php', array(
        ));
    }

    /**
     * @Route("/login")
     */
    public function loginAction()
    {
        $helper = $this->get('security.authentication_utils');
        $error = $helper->getLastAuthenticationError();
        if ($error instanceof BadCredentialsException) {
            $error = new \Exception('Неверный логин или пароль');
        }

        return $this->render('AppBundle:Default:login.html.php', array(
            // last username entered by the user (if any)
            'last_username' => $helper->getLastUsername(),
            // last authentication error (if any)
            'error' => $error ? $error->getMessage() : null
        ));
    }

    /**
     * @Route("/landing")
     * @Route("/new/landing")
     * @Route("/other-new/landing")
     */
    public function landingAction()
    {
        $wallpapers = $this->getDoctrine()->getRepository('AppBundle:Wallpaper')
            ->findBy([ 'points' => [1,2], 'shop' => 'kgb', 'marketPlan' => 0.09434], null, 20, rand(0,1000));
        return $this->render('AppBundle:Default:landing.html.php', array(
            'wallpapers' => $wallpapers
        ));
    }

    /**
     * @Route("/collection-page")
     * @Route("/new/collection-page/{vendor}")
     * @Route("/other-new/collection-page")
     */
    public function collectionPageAction($vendor)
    {

        $collection = $this->getDoctrine()->getRepository('AppBundle:ComplectData')
            ->findOneByVendorCode($vendor)->getCollectionCode();
        $vendors = $this
            ->getDoctrine()
            ->getRepository('AppBundle:ComplectData')
            ->createQueryBuilder('cd')
            ->select(' 
                 w.shop, 
                w.vendorCode, 
                w.manufacturer, 
                w.speed,
                w.points,
                w.successfull, 
                w.dateTime, 
                w.image, 
                w.country, 
                COALESCE(w.size , 0.53) as size, 
                w.catalog,
                w.id, 
                w.uuid,
                w.price,
                w.priceOld,
                w.glitter,
                w.currency,
                ROUND (wc.count/w.marketPlan , 2)  as m_count,
                ROUND (wc.totalCount/w.marketPlan , 2)  as m_totalCount,
                (w.priceOld - w.price) as halyava,
                ROUND (w.priceOld*w.marketPlan , 2)  as m_old_price,
                ROUND (w.price*w.marketPlan , 2)  as m_price,
                ROUND (1/w.marketPlan/COALESCE(nullif(w.size, 0) , 0.53), 2)  as height,
                wc.count,
                wc.totalCount,
                cd.collectionCode
                ')
            ->leftJoin(
                'AppBundle:Wallpaper',
                'w',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'w.vendorCode = cd.vendorCode'
            )
            ->leftJoin(
                'AppBundle:WallpaperCount',
                'wc',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'wc.wallpaperUuid = w.uuid AND w.shop = wc.shop'
            )
            ->where('cd.collectionCode = :collectionCode')
            ->andWhere('w.shop = :shop')
            ->setParameter('shop',  $this->get('session')->get('shop'))
            ->setParameter('collectionCode', $collection)
            ->orderBy('cd.collectionCode', 'ASC')
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        $catalogs = [];
        $image = [];

        foreach ($vendors as $vendor) {
            $catalogs[$vendor['collectionCode']]['vendors'][] = $vendor;
            $catalogs[$vendor['collectionCode']]['country'] = $vendor['country'];
            $catalogs[$vendor['collectionCode']]['manufacturer'] = $vendor['manufacturer'];
            if(!$image) {
                $image = $this
                    ->getDoctrine()
                    ->getRepository('AppBundle:Catalogs')
                    ->createQueryBuilder('c')
                    ->select('c.image')
                    ->where('c.name = :name')
                    ->andWhere('c.image IS NOT NULL')
                    ->setParameter('name', $vendor['catalog'])
                    ->getQuery()
                    ->getResult(Query::HYDRATE_ARRAY);
            }

            if(empty($image[0]['image'])) {
                $image = $this
                    ->getDoctrine()
                    ->getRepository('AppBundle:NotebookImage')
                    ->createQueryBuilder('ni')
                    ->select('ni.image')
                    ->leftJoin(
                        'AppBundle:Notebooks',
                        'n',
                        \Doctrine\ORM\Query\Expr\Join::WITH,
                        'n.uuid = ni.notebook'
                    )
                    ->where('n.catalogCode = :catalog')
                    ->andWhere('ni.image IS NOT NULL')
                    ->setParameter('catalog', $vendor['catalog'])
                    ->getQuery()
                    ->getResult(Query::HYDRATE_ARRAY);
            }

            if(empty($image[0]['image'])) {
                $image[0]['image'] = $vendor['image'];
                $image[1]['image'] = $vendors[1]['image'];
            }

            $catalogs[$vendor['collectionCode']]['image'] = $image;
        }
        return $this->render('AppBundle:Wallpaper:collectionPage.html.php', array(
            'catalog' => $catalogs
        ));
    }

    /**
     * @Route("/updates")
     * @Route("/new/updates")
     * @Route("/other-new/updates")
     */
    public function updatesAction()
    {
        return $this->render('AppBundle:Default:updates.html.php', array(
        ));
    }

    private function updateSession()
    {
        $session = $this->get('session');
        $shop = $session->get('shop');
        if(!$shop) {
            $session->set('shop', 'kgb');
        }

        $nomenclaturesObj = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Wallpaper')
            ->createQueryBuilder('w')
            ->select('DISTINCT w.nomenclature')
            ->where('w.shop = :shop')
            ->setParameter('shop', $session->get('shop'))
            ->orderBy('w.nomenclature', 'DESC')
            ->getQuery()
            ->getResult();

        $nomenclatures = [];

        foreach ($nomenclaturesObj as $nomenclature) {
            $nomenclature = $nomenclature['nomenclature'];
            switch($nomenclature) {
                case 'Обои':
                    $nomenclatures[] = [
                        'nomenclature' =>  $nomenclature,
                        'href' => 'landing' ,
                        'children' => []
                    ];
                    break;
                default:
                    $nomenclatures[] = ['nomenclature' =>  $nomenclature, 'href' => 'landing', 'children' => []];
                    break;
            }
        }

        $shops = $this->getDoctrine()
            ->getRepository('AppBundle:Shop')
            ->findAll();

        $shopArray = ['Кыргызстан' => []];

        foreach ($shops as $shop) {
            $shopArray[$shop->getCountry()][$shop->getCity()][] = [
                'shop' => $shop->getName(),
                'contact' => $shop->getPhoneNumber()
            ];
        }
        $session->set('nomenclatures', $nomenclatures);
        $session->set('shops', $shopArray);
    }
}