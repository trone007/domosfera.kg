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
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Изначально контроллер задумывался как обработчик для /catalog
 * Затем было предложено формировать компаньоны. (изначально не планировавшиеся тут)
 * В связи с этим к изначальному функционалу работы с фильтрами каталогов был добавлен функционал сохранения компаньонов.
 *
 * Часть методов контроллера требует доработки
 *      1. Привязка ко конкретному магазину.
 * Часть методов требует рефакторинга.
 *
 * @author scouserlfc91@gmail.com
 */
class WallpaperController extends Controller
{

    private function getDictionary($dictionary)
    {
        return $this->getDoctrine()
            ->getRepository('AppBundle:Wallpaper')
            ->createQueryBuilder('w')
            ->select("DISTINCT(w.{$dictionary})")
            ->where('w.shop = :shop')
            ->andWhere('w.nomenclature = :nomenclature')
            ->setParameter('shop', 'kgb')
            ->setParameter('nomenclature', $this->get('session')->get('nomenclature') ?? 'Обои')
            ->getQuery()
            ->getResult();
    }
    /**
     * @Route("/")
     * @Route("/catalog")
     * @Route("/new/catalog")
     * @Route("/other-new/catalog")
     */
    public function catalogAction()
    {
        $this->get('session')->set('nomenclature', $_GET['nomenclature']?? 'Обои');
        try {
            $query = $this->getDoctrine()
                ->getRepository('AppBundle:Wallpaper')
                ->createQueryBuilder('w')
                ->select('MAX(w.price) AS max_price, MIN(w.price) AS min_price, MAX( ROUND(w.price * w.marketPlan, 2) )as max_m_price, MIN( ROUND(w.price * w.marketPlan, 2) )as min_m_price')
                ->where('w.shop = :shop')
                ->andWhere('w.nomenclature = :nomenclature')
                ->setParameter('shop', 'kgb')
                ->setParameter('nomenclature', $this->get('session')->get('nomenclature') ?? 'Обои')
                ->setMaxResults(1);
            $maxMin = $query->getQuery()->getResult()[0];

        } catch (\Exception $ex) {
            echo $ex->getMessage();
        }

        return $this->render('AppBundle:Wallpaper:index.html.php', array(
            'maxMin'    => $maxMin,
            'pictures'  => $this->getDictionary('picture'),
            'basises'   => $this->getDictionary('basis'),
            'types'   =>  $this->getDictionary('type'),
            'styles'   => $this->getDictionary('style'),
            'countries'   => $this->getDictionary('country')
        ));
    }

    /**
     * @Route("/favorites")
     * @Route("/new/favorites")
     * @Route("/other-new/favorites")
     */
    public function favoritesAction()
    {
        return $this->render('AppBundle:Wallpaper:favorite.html.php', array(
        ));
    }


    /**
     * @Route("/get-favorites")
     * @Method({"POST"})
     */
    public function searchFavoritesAction(Request $request)
    {
        $query = json_decode($request->request->get('query'));

        $vendors = isset($query->vendors) ? $query->vendors : null;
        $wallpapers = $this->getDoctrine()->getRepository('AppBundle:Wallpaper')
            ->createQueryBuilder('w')
            ->select(
                'w.shop, 
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
                wc.totalCount
                ')
            ->leftJoin(
                'AppBundle:WallpaperCount',
                'wc',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'wc.wallpaperUuid = w.uuid AND w.shop = wc.shop'
            )
            ->where('w.shop = :shop')
            ->andWhere('w.vendorCode IN (:vendors)')
            ->setParameter('shop', 'kgb')
            ->setParameter('vendors', $vendors);


        return new Response(json_encode($wallpapers->getQuery()->getResult(Query::HYDRATE_ARRAY)));
    }
    /**
     * @Route("/get-wallpapers")
     * @Method({"POST"})
     */
    public function filterAction(Request $request)
    {
        $query = json_decode($request->request->get('query'));
        $priceStart = isset($query->priceStart) ? $query->priceStart : null;
        $priceFinish = isset($query->priceFinish) ? $query->priceFinish : null;

        $priceType = isset($query->priceType) ? $query->priceType : null;


        $vendor = isset($query->vendor) ? $query->vendor : null;
        $orderBy = isset($query->orderBy) ? $query->orderBy : null;

        $colors = isset($query->colors) ? $query->colors : null;
        $pictures = isset($query->pictures) ? $query->pictures : null;
        $style = isset($query->style) ? $query->style : null;
        $sizes = isset($query->sizes) ? $query->sizes : null;
        $countries = isset($query->countries) ? $query->countries : null;
        $halyava = isset($query->halyava) ? $query->halyava : null;
        $hot = isset($query->hot) ? $query->hot : null;
        $new = isset($query->new) ? $query->new : null;

        $type = isset($query->type) ? $query->type : null;
        $basis = isset($query->basis) ? $query->basis : null;
        try {
            $wallpapers = $this->getDoctrine()->getRepository('AppBundle:Wallpaper')
                ->createQueryBuilder('w')
                ->select(
                    'w.shop, 
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
                wc.totalCount
                ')
                ->leftJoin(
                    'AppBundle:WallpaperCount',
                    'wc',
                    \Doctrine\ORM\Query\Expr\Join::WITH,
                    'wc.wallpaperUuid = w.uuid AND w.shop = wc.shop'
                )
                ->where('w.shop = :shop')
                ->andWhere('w.nomenclature = :nomenclature')
                ->setParameter('shop', 'kgb')
                ->setParameter('nomenclature', $this->get('session')->get('nomenclature') ?? 'Обои');

            if($priceStart) {
                if ($priceType) {
                    $wallpapers->andWhere('ROUND(w.price*w.marketPlan, 2) >= :priceStart AND ROUND(w.price*w.marketPlan, 2) - 1 <= :priceFinish');
                } else {
                    $wallpapers->andWhere('w.price BETWEEN :priceStart AND :priceFinish');
                }
                $wallpapers
                    ->setParameter('priceStart', $priceStart)
                    ->setParameter('priceFinish', $priceFinish+1);
            }

            if ($vendor) {
                $wallpapers->andWhere("w.vendorCode like :vendor")
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

                    $wallpapers->setParameter('color' . $i++, $color);
                }

                $wallpapers->andWhere($queryString);
            }

            if ($pictures) {
                $i = 0;

                $queryString = '';
                foreach ($pictures as $picture) {
                    $queryString .= 'w.picture = :picture' . $i;

                    if ($i < count($pictures) - 1) {
                        $queryString .= ' OR ';
                    }

                    $wallpapers->setParameter('picture' . $i++, $picture);
                }

                $wallpapers->andWhere($queryString);
            }

            if ($sizes) {
                $i = 0;
                $queryString = '';
                foreach ($sizes as $size) {
                    $queryString .= 'w.size = :size' . $i;

                    if ($i < count($sizes) - 1) {
                        $queryString .= ' OR ';
                    }
                    $wallpapers->setParameter('size' . $i++, round($size, 2));
                }

                $wallpapers->andWhere($queryString);
            }

            if ($countries) {
                $i = 0;

                $queryString = '';
                foreach ($countries as $country) {
                    $queryString .= 'w.country = :country' . $i;

                    if ($i < count($countries) - 1) {
                        $queryString .= ' OR ';
                    }

                    $wallpapers->setParameter('country' . $i++, $country);
                }

                $wallpapers->andWhere($queryString);
            }

            if ($style) {
                $i = 0;

                $queryString = '';
                foreach ($style as $st) {
                    $queryString .= 'w.style = :style' . $i;

                    if ($i < count($style) - 1) {
                        $queryString .= ' OR ';
                    }

                    $wallpapers->setParameter('style' . $i++, $st);
                }

                $wallpapers->andWhere($queryString);
            }

            if ($type) {
                $i = 0;

                $queryString = '';
                foreach ($type as $st) {
                    $queryString .= 'w.type = :type' . $i;

                    if ($i < count($type) - 1) {
                        $queryString .= ' OR ';
                    }

                    $wallpapers->setParameter('type' . $i++, $st);
                }

                $wallpapers->andWhere($queryString);
            }

            if(!empty($orderBy) && count((array)$orderBy) > 0) {
                $wallpapers->orderBy('w.' . $orderBy->column, $orderBy->type);
                if($orderBy->column == 'price') {
                    if($priceType) {
                        $wallpapers->orderBy('ROUND(w.price*w.marketPlan, 2)', $orderBy->type);
                    }
                }
            }

            if ($halyava) {
                $wallpapers->andWhere('w.points = 1 OR w.points = 2');
                $wallpapers->orderBy('halyava', 'DESC');
            }
            if ($hot) {
                $wallpapers->andWhere('w.points = 5 OR w.points = 4');
                $wallpapers->orderBy('w.successfull', 'DESC');
            }
            if ($new) {
                $wallpapers->andWhere('w.points = 7');
                $wallpapers->orderBy('w.dateTime', 'DESC');
            }

            if ($basis) {
                $i = 0;

                $queryString = '';
                foreach ($basis as $st) {
                    $queryString .= 'w.basis = :basis' . $i;

                    if ($i < count($basis) - 1) {
                        $queryString .= ' OR ';
                    }

                    $wallpapers->setParameter('basis' . $i++, $st);
                }
                $wallpapers->andWhere($queryString);
            }

            return new Response(json_encode($wallpapers->getQuery()
                ->getResult(Query::HYDRATE_ARRAY)));
        } catch (\Exception $ex) {
            echo $ex->getMessage(), $ex->getLine();
            die;
        }
    }
    /**
     * @Route("/get-price")
     * @Method({"POST"})
     */
    public function priceAction(Request $request)
    {
//        $
//        $wallpapers =
        $query = json_decode($request->request->get('query'));

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
        try {
            $wallpapers = $this->getDoctrine()
                ->getRepository('AppBundle:Wallpaper')
                ->createQueryBuilder('w')
                ->select('MAX(w.price) AS max_price, 
                    MIN(w.price) AS min_price, 
                    MAX( ROUND(w.price * w.marketPlan, 2) )as max_m_price, 
                    MIN( ROUND(w.price * w.marketPlan, 2) )as min_m_price'
                )
                ->where('w.shop = :shop')
                ->andWhere('w.nomenclature = :nomenclature')
                ->setParameter('shop', 'kgb')
                ->setParameter('nomenclature', $this->get('session')->get('nomenclature') ?? 'Обои');

            if ($colors) {
                $i = 0;

                $queryString = '';
                foreach ($colors as $color) {
                    $queryString .= 'w.color1 = :color' . $i . ' OR w.color2 = :color' . $i . ' OR w.color3 = :color' . $i;

                    if ($i < count($colors) - 1) {
                        $queryString .= ' OR ';
                    }

                    $wallpapers->setParameter('color' . $i++, $color);
                }

                $wallpapers->andWhere($queryString);
            }

            if ($pictures) {
                $i = 0;

                $queryString = '';
                foreach ($pictures as $picture) {
                    $queryString .= 'w.picture = :picture' . $i;

                    if ($i < count($pictures) - 1) {
                        $queryString .= ' OR ';
                    }

                    $wallpapers->setParameter('picture' . $i++, $picture);
                }

                $wallpapers->andWhere($queryString);
            }
//
            if ($sizes) {
                $i = 0;
                $queryString = '';
                foreach ($sizes as $size) {
                    $queryString .= 'w.size = :size' . $i;

                    if ($i < count($sizes) - 1) {
                        $queryString .= ' OR ';
                    }
                    $wallpapers->setParameter('size' . $i++, round($size, 2));
                }

                $wallpapers->andWhere($queryString);
            }
//
            if ($countries) {
                $i = 0;

                $queryString = '';
                foreach ($countries as $country) {
                    $queryString .= 'w.country = :country' . $i;

                    if ($i < count($countries) - 1) {
                        $queryString .= ' OR ';
                    }

                    $wallpapers->setParameter('country' . $i++, $country);
                }

                $wallpapers->andWhere($queryString);
            }
//
            if ($textures) {
                $i = 0;

                $queryString = '';
                foreach ($textures as $texture) {
                    $queryString .= 'w.texture = :texture' . $i;

                    if ($i < count($textures) - 1) {
                        $queryString .= ' OR ';
                    }

                    $wallpapers->setParameter('texture' . $i++, $texture);
                }

                $wallpapers->andWhere($queryString);
            }
//
            if ($halyava) {
                $wallpapers->andWhere('w.points = 5 ');
//                $wallpapers->orderBy('halyava', 'DESC');
            }
            if ($hot) {
                $wallpapers->andWhere('w.points = 1');
//                $wallpapers->orderBy('w.successfull', 'DESC');
            }
            if ($new) {
                $wallpapers->andWhere('w.points = 7');
//                $wallpapers->orderBy('w.dateTime', 'DESC');
            }
//
            if ($glitter != "") {
                if ($glitter == "null") {
                    $wallpapers->andWhere('w.glitter is NULL');
                } else {
                    $wallpapers->andWhere('w.glitter = :glitter')
                        ->setParameter('glitter', $glitter);
                }
            }

            return new Response(json_encode($wallpapers->setMaxResults(1)->getQuery()->getResult(Query::HYDRATE_ARRAY)[0]));
        } catch (\Exception $ex) {
            echo $ex->getMessage();
            die;
        }
    }

    /**
     * @Route("/wallpaper/{id}", name="wallpaper")
     * @Route("/new/wallpaper/{id}", name="wallpaper_small")
     * @Route("/other-new/wallpaper/{id}", name="wallpaper_large")
     * @Method({"GET"})
     */
    public function wallpaperAction($id)
    {

        try {
            if(!$wallpaper = $this->getDoctrine()->getRepository('AppBundle:Wallpaper')->findOneBy([
                'vendorCode' => $id,
                'shop' => 'kgb'
                ]
            )) {
                $wallpaper = $this->getDoctrine()->getRepository('AppBundle:Wallpaper')->findOneBy([
                        'id' => $id,
                        'shop' => 'kgb'
                    ]
                );
            }
        } catch (\Exception $ex) {
            $wallpaper = new Entities\Wallpaper();
        }

        $url = 'http://www.gallery.kg/api/'
            . urlencode('домосфера') . '/'
            . urlencode('карточка-товара') . '?'
            . urlencode('магазин') . '=' . $wallpaper->getShop() . '&'
            . urlencode('id') . '=' . $wallpaper->getVendorCode();

        $count = @simplexml_load_file($url)->Остатки->Остаток;

        $complect = $this->getDoctrine()->getRepository('AppBundle:Complect')->findOneByVendorCode($wallpaper->getVendorCode());
        $complectGroup = $this->getDoctrine()->getRepository('AppBundle:ComplectGroup')->findBy(
            ['complectCode' => $complect]
        );

        $companion = $this->getDoctrine()->getRepository('AppBundle:Companion')->findOneByVendorCode($wallpaper->getVendorCode());
        $companionGroup = $this->getDoctrine()->getRepository('AppBundle:CompanionGroup')->findBy(
            ['companionCode' => $companion]
        );

        $queryParams = [];

        foreach($complectGroup as $collection) {
            $queryParams[] = $collection->getVendorCode();
        }

        foreach($companionGroup as $collection) {
            $queryParams[] = $collection->getVendorCode();
        }

        $complectsData = $this->getDoctrine()->getRepository('AppBundle:Wallpaper')->findBy([
            'vendorCode' => $queryParams,
            'shop'       => 'kgb'
        ]);

        $sortOrder = [
            'kgb',
            'kgopt1',
            'kgb5',
            'kgb6',
            'kgb3',
            'kgb2',
            'kgb8',
            'kgto',
            'kgkb',
            'kzopt1',
            'kzsh',
            'kzsh2',
            'kzsh3',
            'kztt82',
            'kgopt2',
            'kzopt2',
            'kzao',
            'kzaa'
        ];

        foreach($sortOrder as $so) {
            $shopData[$so] = [
                'code' => "",
                'class' => "",
                'name' => "",
                'count' => 0,
                'lot' => "",
                'reserve' => 0
            ];
        }

        foreach ($count as $shop) {
            $name = $this->getDoctrine()->getRepository('AppBundle:Shop')->findOneByUuid($shop->Магазин);
            if(!$name) continue;
            $name = $name->getName();

            $shopData[(string)$shop->Магазин]['code'] = (string)$shop->Магазин;
            $shopData[(string)$shop->Магазин]['class'] = (string)$shop->Классификатор;
            $shopData[(string)$shop->Магазин]['name'] = $name;
            $shopData[(string)$shop->Магазин]['lot'] = (string)$shop->Характеристика;

            $shopData[(string)$shop->Магазин]['count'] += (float)$shop->СвободныйОстаток;
            $shopData[(string)$shop->Магазин]['reserve'] += (float)$shop->Резерв;

        }

        foreach($shopData as $key => $sd) {
            if(strlen($sd['code']) == 0) {
                unset($shopData[$key]);
            }
        }
        $user = $this->get('security.token_storage')
            ->getToken()
            ->getUser();

        if(is_string($user)) {
            $user = '';
        }

        return $this->render('AppBundle:Wallpaper:wallpaper.html.php', array(
            'wallpaper'    => $wallpaper,
            'shops' => $shopData,
            'complectsData' => $complectsData,
            'user' => $user
        ));
    }

    /**
     * @Route("/wallpaper-api/{id}", name="wallpaper-api")
     * @Method({"GET"})
     */
    public function wallpaperApiAction($id)
    {
        if(!$wallpaper = $this->getDoctrine()->getRepository('AppBundle:Wallpaper')->findOneBy([
                'vendorCode' => $id,
                'shop' => 'kgb'
            ]
        )) {
            $wallpaper = $this->getDoctrine()->getRepository('AppBundle:Wallpaper')->findOneBy([
                    'id' => $id,
                    'shop' => 'kgb'
                ]
            );
        }

        $qb = $this->getDoctrine()->getRepository('AppBundle:Wallpaper')->
        createQueryBuilder('w')
            ->select('w')
            ->where('w =:wallpaper')->setParameter('wallpaper', $wallpaper)
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        $url = 'http://www.gallery.kg/api/'
            . urlencode('домосфера') . '/'
            . urlencode('карточка-товара') . '?'
            . urlencode('магазин') . '=' . $wallpaper->getShop() . '&'
            . urlencode('id') . '=' . $wallpaper->getVendorCode();

        $count = @simplexml_load_file($url)->Остатки->Остаток;

        $complect = $this->getDoctrine()->getRepository('AppBundle:Companion')->findOneByVendorCode($wallpaper->getVendorCode());
        $collections = $this->getDoctrine()->getRepository('AppBundle:CompanionGroup')->findBy(
            ['companionCode' => $complect]
        );

        $queryParams = [];

        foreach($collections as $collection) {
            $queryParams[] = $collection->getVendorCode();
        }

        $complectsData = $this->getDoctrine()->getRepository('AppBundle:Wallpaper')
            ->createQueryBuilder('cg')
            ->select('cg')
            ->where('cg.vendorCode IN (:vendorCode)')->setParameter('vendorCode', $queryParams)
            ->andWhere("cg.shop = 'kgb'")
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        $sortOrder = [
            'kgb',
            'kgopt1',
            'kgb5',
            'kgb6',
            'kgb3',
            'kgb2',
            'kgb8',
            'kgto',
            'kgkb',
            'kzopt1',
            'kzsh',
            'kzsh2',
            'kzsh3',
            'kztt82',
            'kgopt2',
            'kzopt2',
            'kzao',
            'kzaa'
        ];

        foreach($sortOrder as $so) {
            $shopData[$so] = [
                'class' => "",
                'name' => "",
                'count' => 0,
                'lot' => "",
                'reserve' => 0
            ];
        }

        foreach ($count as $shop) {
            $name = $this->getDoctrine()->getRepository('AppBundle:Shop')->findOneByUuid($shop->Магазин);
            if(!$name) continue;
            $name = $name->getName();

            $shopData[(string)$shop->Магазин]['code'] = (string)$shop->Магазин;
            $shopData[(string)$shop->Магазин]['class'] = (string)$shop->Классификатор;
            $shopData[(string)$shop->Магазин]['name'] = $name;
            $shopData[(string)$shop->Магазин]['lot'] = (string)$shop->Характеристика;

            $shopData[(string)$shop->Магазин]['count'] += (float)$shop->СвободныйОстаток;
            $shopData[(string)$shop->Магазин]['reserve'] += (float)$shop->Резерв;

        }

        foreach($shopData as $key => $sd) {
            if(strlen($sd['code']) == 0) {
                unset($shopData[$key]);
            }
        }

        return new Response(json_encode(array(
                    'wallpaper'    => $qb,
                    'shops' => $shopData,
                    'complectsData' => $complectsData
                )
            )
        );
    }


    /**
     * @Route("/image")
     * @Method({"GET"})
     */
    public function renderImageAction(Request $request)
    {

        $id = $request->get('id');
        $width = (int)$request->get('width');
        $height = (int)$request->get('height');

        header('Content-type: image/jpeg');
        ob_implicit_flush(true);
        ob_start();
//        $showImage = new Process("php /home/denis/project/bin/console app:image-get -- {$id} {$width} {$height}");
//        $showImage->start();
////
//
////
//
//        $showImage->wait();
//        echo base64_decode($showImage->getOutput());
        $dir = '/home/denis/project/web/tmp/';
        $filename = $dir . $id . $width.'x'.$height.'.jpg';
        if(file_exists($filename)) {
            echo file_get_contents($filename);
        } else {
            $process = new Process("php /home/denis/project/bin/console app:image-update -- {$id} {$width} {$height}");
            $process->start();
            $process->wait();

            echo base64_decode($process->getOutput());

        }
        ob_flush();

        ob_end_flush();
        ob_clean();
        return new Response('');
    }

    /**
     * @Route("/companion/")
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

        $sql = 'SELECT COUNT (cd.*) - COUNT (c.*) as not_handled FROM companion c
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

        return $this->render('AppBundle:Wallpaper:companion.html.php', array(
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

        $sql = 'SELECT COUNT (cd.*) - COUNT (c.*) as not_handled FROM companion c
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

        $sql = 'SELECT COUNT (cd.*) - COUNT (c.*) as not_handled FROM companion c
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

        $sql = 'SELECT COUNT (cd.*) - COUNT (c.*) as not_handled FROM companion c
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
    public function getVendorsAction(Request $request)
    {
        $query = json_decode($request->request->get('query'));

        $catalog = base64_decode($query->catalog);

        $vendors = $this
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
                'AppBundle:Companion',
                'c',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'c.vendorCode = w.vendorCode'
            )
            ->where('w.catalog=:catalog')
            ->setParameter('catalog', $catalog)
            ->orderBy('pictureMain DESC, w.notebook ASC, w.vendorCode')
            ->getQuery()
            ->getResult();

        return new Response(json_encode(['vendors' => $vendors, 'complects' => $this->getComplect(false, $catalog)]));
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

        $vendors = $query->vendors->node;

        $complect = $doctrine->getRepository('AppBundle:Companion')->findOneByVendorCode($root);

        if(!$complect) {
            $complect = new Entities\Companion();

            $doctrine->getManager()->persist($complect);

            $complect->setComplect($catalog . '-' . $complect->getId());
            $complect->setVendorCode($root);

            $doctrine->getManager()->persist($complect);
            $doctrine->getManager()->flush();
        } else {
            $currently = $doctrine->getRepository('AppBundle:CompanionGroup')->findByCompanionCode($complect);

            foreach ($currently as $current) {
                $doctrine->getManager()->remove($current);
            }

            $doctrine->getManager()->flush();
        }

        foreach ($vendors as $vendor) {
            $collectionComplect = new Entities\CompanionGroup();
//            $collectionComplect->setCollectionCode($collection);
            $collectionComplect->setCompanionCode($complect);
            $collectionComplect->setVendorCode($vendor);

            $doctrine->getManager()->persist($collectionComplect);
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

        $sql = 'SELECT COUNT (cd.*) - COUNT (c.*) as not_handled, COUNT (c.*) as handled FROM companion c
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

        $wallpapers = $this->getDoctrine()->getRepository('AppBundle:ComplectData')
            ->createQueryBuilder('w')
            ->select(
                'w.picture, 
                w.vendorCode, 
                w.texture, 
                w.image,
                w.notebook,
                cc.vendorCode as rootVendor
                ')
            ->leftJoin(
                'AppBundle:CompanionGroup',
                'c',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'c.vendorCode = w.vendorCode'
            )->leftJoin(
                'AppBundle:Companion',
                'cc',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'cc.id = IDENTITY(c.companionCode)'
            );

        $roots = $this->getDoctrine()->getRepository('AppBundle:Companion')
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
