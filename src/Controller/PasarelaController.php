<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\OrdenesPaypal;
class PasarelaController extends AbstractController
{
    private $client;
    private $em;
    public function __construct(HttpClientInterface $client, EntityManagerInterface $em){
        $this->client = $client;
        $this->em = $em;
    }
    #[Route('/pasarela', name: 'pasarela')]
    public function pasarela(): Response
    {
        return $this->render('pasarela/index.html.twig');
    }

    #[Route('/pasarela/paypal', name: 'pasarela_paypal')]
    public function paypal(): Response
    {
        $response_token = $this->client->request(
            'POST',$_ENV['PAYPAL_BASE_URI']."/v1/oauth2/token",
            [
                'body' => 'grant_type=client_credentials',
                'auth_basic'=> [
                    $_ENV['PAYPAL_CLIENT_ID'],
                    $_ENV['PAYPAL_CLIENT_SECRET']
                ]
            ]
        );

        $token = $response_token->toArray();
        $monto=100;
        $entity = new OrdenesPaypal();
        $entity->setToken($token["access_token"]);
        $entity->setOrden('');
        $entity->setNombre('');
        $entity->setMonto($monto);
        $entity->setCountryCode(0);
        $entity->setPaypalRequest(0);
        $entity->setEstado(0);
        $entity->setFecha(new \DateTime());
        $entity->setIdCaptura('');
        $this->em->persist($entity);
        $this->em->flush();

        
        //crear orden;
         $response = $this->client->request('POST', $_ENV['PAYPAL_BASE_URI']."/v2/checkout/orders",
         [
            "headers"=>[
                "Content-Type"=>"application/json",
                "PayPal-Request-Id"=>'order_'.$entity->getId(),
                "Authorization"=>"Bearer ".$token["access_token"]
            ],
            "json"=>[
            
                "purchase_units"=> [
                    [
                        "amount"=> [
                            "currency_code"=> "USD",
                            "value"=> $monto
                        ],
                        "reference_id"=> "reference_".$entity->getId()
                    ]
                ],
                "intent"=> "CAPTURE",
                "payment_source"=> [
                    "paypal"=> [
                        "experience_context"=> [
                            "payment_method_preference"=> "IMMEDIATE_PAYMENT_REQUIRED",
                            "payment_method_selected"=> "PAYPAL",
                            "brand_name"=> "Tamila",
                            "locale"=> "es-ES",
                            "landing_page"=> "LOGIN",
                            "shipping_preference"=> "NO_SHIPPING",
                            "user_action"=> "PAY_NOW",

                            //URLS IMPORTANTES QUE CONFIRMAN O CANCELAN LA COMPRA
                            "return_url"=> "https://127.0.0.1:8000/pasarela/paypal/respuesta",
                            "cancel_url"=>"http://127.0.0.1:8000/pasarela/paypal/cancelado"
                        ]
                    ]
                ]
            
            ]
         ]
        );

        $content = $response->toArray();
        //actualizar el registro de la orden de comprar en la tabla OrdenesPaypal
        $entity->setOrden($content["id"]);
        $this->em->flush();

        return $this->render('pasarela/paypal.html.twig',[
            "token" => $token["access_token"],
            "content" => $content,
            "entity" => $entity
        ]);
    }
    #[Route('/pasarela/paypal/respuesta', name: 'paypal_respuesta')]
    public function paypal_respuesta(): Response
    {
        $id = $_GET['token'];
        $orden = $this->em->getRepository(OrdenesPaypal::class)->findOneBy(['orden'=>$id]);

        if(!$orden){
            throw $this->createNotFoundException("Esta URL no existe");
        }
        //si el token se caduca hacemos un try - cacth
        try{
            $response = $this->client->request('POST',$_ENV['PAYPAL_BASE_URI'].'/v2/checkout/orders/'.$orden->getOrden().'/capture',[
               "auth_bearer" =>$orden->getToken(),
               "headers"=> [
                    "Content-type"=>"application/json"
               ] 
            ]);

            $content = $response->toArray();
            $orden->setNombre($content["payment_source"]["paypal"]["name"]["given_name"].' '.$content["payment_source"]["paypal"]["name"]["surname"]);
            $orden->setIdCaptura($content["purchase_units"][0]["payments"]["captures"][0]["id"]);
            // $orden->setCountryCode($content["purchase_units"][0]["shipping"]["address"]["country_code"]);
            $orden->setEstado(1);
            $this->em->persist($orden);
            $this->em->flush();
            $estado="okey";
        }catch(\Throwable $th){
            $estado='error';
        }

        return $this->render('pasarela/paypal_respuesta.html.twig',[
            "orden"=>$orden,
            "estado"=>$estado,
            "id"=>$id
        ]);
    }

    #[Route('/pasarela/paypal/cancelado', name: 'paypal_cancelado')]
    public function paypal_cancelado(): Response
    {

        $id = $_GET['token'];

        $orden = $this->em->getRepository(OrdenesPaypal::class)->findOneBy(['orden'=>$id]);

        if(!$orden){
            throw $this->createNotFoundException("Esta URL no existe");
        }

        $orden->setEstado(2);
        $this->em->persist($orden);
        $this->em->flush();
        return $this->render('pasarela/paypal_cancelado.html.twig',[
            "id"=>$id
        ]);
    }
}
