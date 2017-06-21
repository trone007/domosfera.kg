<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Контроллер отвечает за обратную связь между операторами системы домосфера.
 * Сообщения имеют право только оставлять авторизованные пользователи.
 * просмотр сообщений возможен по ссылке /chat.
 *
 * В просмотре сообщений реализованы фильтры по автору и периоду сообщений.
 *
 * Под сообщение подразумевается отзыв пользователя о картинке(комментариев к изменению или правке).
 * Пользователь может закрыть комментарий().
 * Происходит это путем смены статуса сообщения.
 * 2- открыт.
 * 1- закрыт
 *
 * @author scouserlfc91@gmail.com
 */
class ChatController extends Controller
{
    /**
     * @Route("/chat")
     */
    public function chatAction(Request $request)
    {
        $statuses = ['1' => 'Обработано', '2' => 'Не обработано'];
        $usersRep = $this->getDoctrine()->getRepository('AppBundle:Users');
        $users = $usersRep->findAll();

        $user = $request->get('user');
        $vendorCode = $request->get('vendorCode');
        $periodFrom = $request->get('periodFrom');
        $periodTo = $request->get('periodTo');


        $last = $this->getDoctrine()
            ->getRepository('AppBundle:ChatMessages')
            ->createQueryBuilder('cm')
            ->select('cm')
            ->where('cm.dateTime IS NOT NULL');

        if($user) {
//            $userEntity = $usersRep->createQueryBuilder('u')
//                ->select('u')
//                ->where("(LOWER(u.name) like LOWER(:user)) OR (LOWER(u.surname) like LOWER(:user)) OR (LOWER(u.username) like LOWER(:user))")
//                ->setParameter('user', '%' . $user . '%')
//                ->setMaxResults(1)
//                ->getQuery()
//                ->getResult();

            $userEntity = $usersRep->findOneById($user);

            $last->andWhere('cm.user = :user')
            ->setParameter('user', $userEntity);
        }

        if($vendorCode) {
            $last->andWhere('LOWER(cm.vendorCode) LIKE LOWER(:vendorCode)')
                ->setParameter('vendorCode', $vendorCode);
        }

        if($periodTo) {
            $last->andWhere('cm.dateTime BETWEEN :begin AND :end')
                ->setParameter('begin', (new \DateTime($periodFrom))->format('Y-m-d'))
                ->setParameter('end', (new \DateTime($periodTo))->format('Y-m-d'));
        }

        $result = $last
            ->orderBy('cm.dateTime', 'DESC')
            ->getQuery()
            ->getResult();
        return $this->render('AppBundle:Chat:chat.html.php', array(
            'messages' => $result,
            'statuses' => $statuses,
            'periodFrom' => $periodFrom,
            'periodTo' => $periodTo,
            'user'    => $user,
            'users'    => $users,
            'vendorCode'  => $vendorCode
        ));
    }

    /**
     * @Route("/save-message")
     */
    public function saveMessageAction(Request $request)
    {
        $vendorCode = $request->get('vendorCode');
        $message = $request->get('data');

        $chatMessage = new \AppBundle\Entity\ChatMessages();
        $chatMessage->setMessage($message);
        $chatMessage->setDateTime(new \DateTime());
        $chatMessage->setVendorCode($vendorCode);
        $chatMessage->setStatus(2);
        $chatMessage->setUser($this->get('security.token_storage')
        ->getToken()
        ->getUser());


        $this->getDoctrine()->getManager()->persist($chatMessage);
        $this->getDoctrine()->getManager()->flush();
        return new Response(json_encode([ 'success'=> true ]));
    }
    /**
     * @Route("/close-message/{id}")
     */
    public function closeAction($id)
    {
        $message = $this->getDoctrine()->getRepository('AppBundle:ChatMessages')->findOneById($id);

        $message->setStatus(1);
        $this->getDoctrine()->getManager()->persist($message);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirect('/chat');
    }

    /**
     * @Route("/open-message/{id}")
     */
    public function openAction($id)
    {
        $message = $this->getDoctrine()->getRepository('AppBundle:ChatMessages')->findOneById($id);

        $message->setStatus(2);
        $this->getDoctrine()->getManager()->persist($message);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirect('/chat');
    }

    /**
     * @Route("/get-messages")
     */
    public function getMessagesAction(Request $request)
    {
        return $this->render('AppBundle:Chat:get_messages.html.php', array(
            // ...
        ));
    }

}
