<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class OrderSuccessController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, ParameterBagInterface $params)
    {   
        $this->entityManager = $entityManager;
        $this->params = $params;
    }

    /**
     * @Route("/commande/merci/{stripeSessionId}", name="order_validate")
     */
    public function index(Cart $cart, $stripeSessionId): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('home');
        }

        if($order->getState() == 0){
            $cart->remove();
            
            $order->setState(1);
            $this->entityManager->flush();

            $mail=new Mail($this->params);
            $content = "Bonjour ".$order->getUser()->getFirstname()."<br/>Merci pour votre commande.<br><br/>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptatum nulla dolore libero necessitatibus esse aperiam cumque, voluptas laudantium cum deserunt facere cupiditate.";
            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstname(),'Votre commande la Boutique Française est bien validée', $content);
        }

        return $this->render('order_success/index.html.twig', [
            'order' => $order
        ]);
    }
}
