<?php

namespace App\Controller;
// require '../vendor/autoload.php';
// require_once '../dompdf/autoload.inc.php';
// require_once "./vendor/autoload.php";



use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//ENVIAR E-MAIL
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

//fallo en el envío de emails
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;


//http client
use Symfony\Contracts\HttpClient\HttpClientInterface;

use Symfony\Component\HttpFoundation\Request;
use App\Form\PublicacionType;

use App\Form\AccionType;

//FILESYSTEM
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

//PDF
use Dompdf\Dompdf;

class UtilidadesController extends AbstractController
{
    public function __construct(private HttpClientInterface $client,)
    {
    }

    #[Route('/utilidades', name: 'utilidades_inicio')]
    public function index(): Response
    {
        return $this->render('utilidades/index.html.twig');
    }

    #[Route('/utilidades/mail', name: 'utilidades_mail')]
    public function enviar_mail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('monicagarciayadaicela@gmail.com')
            ->to('isabel.migy@gmail.com')
            ->subject('Mi primer e-mail!')
            // el formato del texto del email puede ser text o htm
            ->text('Esto es una pruba de envíos de email con symfony!');
            // ->html('<p>Esto es una pruba de envíos de email con symfony!</p>');

        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            die($e);
        }
        return $this->render('utilidades/mail.html.twig');
    }

    #[Route('/utilidades/api_rest', name: 'utilidades_api_rest')]
    public function api_rest(): Response
    {
        $response = $this->client->request(
            'GET',
            'https://jsonplaceholder.typicode.com/posts'

        );
        
        return $this->render('utilidades/api_rest.html.twig', compact('response'));
    }
    #[Route('/utilidades/api_rest/post', name: 'utilidades_api_rest_post')]
    public function api_rest_post(Request $request): Response
    {

        $form = $this->createForm(PublicacionType::class, null);
        $form -> handleRequest($request);
        $submitedToken = $request->request->get('token');

        if($form->isSubmitted()){
            if($this->isCsrfTokenValid('generico',$submitedToken)){

                $campos= $form->getData();
                $datos = [
                    'title' => $campos['title'],
                    'body' => $campos['body'],
                    'userId' => $campos['userId']
                ];

                if(empty($campos['title']) or empty($campos['body'])or empty($campos['userId'])){
                    $this->addFlash('css','danger');
                    $this->addFlash('respuesta', 'campos vacíos');
                    $this->addFlash('mensaje','rellena todos los campos');
                    return $this->redirectToRoute('utilidades_api_rest_post');

                }
                $response = $this->client->request(
                    'POST',
                    'https://jsonplaceholder.typicode.com/posts',
                    [
                        'json' => $datos
                    ]
                );

                $response = $response->getStatusCode();

                if($response === 201){
                    $this->addFlash('css','success');
                    $this->addFlash('respuesta',$response);
                    $this->addFlash('mensaje','proceso completado con éxito');
                }else{
                    $this->addFlash('css','danger');
                    $this->addFlash('respuesta',$response);
                    $this->addFlash('mensaje','vuelve a intentarlo más tarde');
                    return $this->redirectToRoute('utilidades_api_rest_post');
                }
                return $this->redirectToRoute('utilidades_api_rest_post');

            }
        }

        return $this->render('utilidades/api_rest_post.html.twig', compact('form'));
    }


    #[Route('/utilidades/api_rest_acciones', name: 'utilidades_api_rest_acciones')]
    public function api_rest_acciones(Request $request): Response
    {

        //1 - OBTENIENDO EL TOKEN
        $response = $this->client->request(
            'POST',
            'https://www.api.tamila.cl/api/login',
            [
                'json' => [
                    'correo' => 'info@tamila.cl',
                    'password' => 'p2gHNiENUw'
                ]
            ]

        );

        $responseJson = $response->getContent();
        $responseData = json_decode($responseJson, true, 512, JSON_THROW_ON_ERROR);

        $token = $responseData['token'];


        $response = $this->client->request(
            'GET',
            'https://www.api.tamila.cl/api/categorias',
            [
                'headers' => [
                    'Authorization' => 'Bearer '. $token
                ]
            ]

        );
        
        return $this->render('utilidades/api_rest_acciones.html.twig', compact('response'));
    }

    #[Route('/utilidades/api_rest_añadir', name: 'utilidades_api_rest_añadir')]
    public function añadir(Request $request): Response
    {
        $form = $this->createForm(AccionType::class, null);
        $form -> handleRequest($request);
        $submitedToken = $request->request->get('token');


        if($form->isSubmitted()){


            if($this->isCsrfTokenValid('generico',$submitedToken)){

                //1 - OBTENIENDO EL TOKEN
                $response = $this->client->request(
                    'POST',
                    'https://www.api.tamila.cl/api/login',
                    [
                        'json' => [
                            'correo' => 'info@tamila.cl',
                            'password' => 'p2gHNiENUw'
                        ]
                    ]
        
                );

                $responseJson = $response->getContent();
                $responseData = json_decode($responseJson, true, 512, JSON_THROW_ON_ERROR);
                $token = $responseData['token'];


                //2 - AÑADIR ELEMENTO
                $campos= $form->getData();
                $datos = [
                    'nombre' => $campos['nombre']
                ];

                $response = $this->client->request(
                    'POST',
                    'https://www.api.tamila.cl/api/categorias',
                    [
                        'headers' => [
                            'Authorization' => 'Bearer '.$token
                        ],
                        'json' => $datos
                    ]

                );

                $response = $response->getStatusCode();

                if($response === 201){
                    $this->addFlash('css','success');
                    $this->addFlash('respuesta',$response);
                    $this->addFlash('mensaje','proceso completado con éxito');
                }else{
                    $this->addFlash('css','danger');
                    $this->addFlash('respuesta',$response);
                    $this->addFlash('mensaje','vuelve a intentarlo más tarde');
                }
                return $this->redirectToRoute('utilidades_api_rest_añadir');

            }
        }

        return $this->render('utilidades/api_rest_añadir.html.twig', compact('form'));
    }



    #[Route('/utilidades/api_rest_modificar/{id}', name: 'utilidades_api_rest_modificar')]
    public function modificar(Request $request, int $id): Response
    {
        //1 - OBTENIENDO EL TOKEN
        $response = $this->client->request(
            'POST',
            'https://www.api.tamila.cl/api/login',
            [
                'json' => [
                    'correo' => 'info@tamila.cl',
                    'password' => 'p2gHNiENUw'
                ]
            ]

        );

        $responseJson = $response->getContent();
        $responseData = json_decode($responseJson, true, 512, JSON_THROW_ON_ERROR);
        $token = $responseData['token'];


        $form = $this->createForm(AccionType::class, null);
        $form -> handleRequest($request);
        $submitedToken = $request->request->get('token');

        //OBTENIENDO INFORMACIÓN ANTERIOR
        $datos = $this->client->request(
            'GET',
            'https://www.api.tamila.cl/api/categorias/'.$id,
            [
                'headers' => [
                    'Authorization' => 'Bearer '.$token
                ]
            ]

        );
        if($form->isSubmitted()){


            if($this->isCsrfTokenValid('generico',$submitedToken)){


                

                
                //2 - AÑADIR ELEMENTO
                $campos= $form->getData();
                $datos = [
                    'nombre' => $campos['nombre']
                ];

                $response = $this->client->request(
                    'PUT',
                    'https://www.api.tamila.cl/api/categorias/'.$id,
                    [
                        'headers' => [
                            'Authorization' => 'Bearer '.$token
                        ],
                        'json' => $datos
                    ]

                );

                $response = $response->getStatusCode();

                if($response === 201){
                    $this->addFlash('css','success');
                    $this->addFlash('respuesta',$response);
                    $this->addFlash('mensaje','proceso completado con éxito');
                }else{
                    $this->addFlash('css','danger');
                    $this->addFlash('respuesta',$response);
                    $this->addFlash('mensaje','vuelve a intentarlo más tarde');
                }
                return $this->redirectToRoute('utilidades_api_rest_modificar', ['id' => $id]);

            }
        }

        return $this->render('utilidades/api_rest_modificar.html.twig', ['form' => $form,  'datos' => $datos]);
    }


    #[Route('/utilidades/api_rest_eliminar/{id}', name: 'utilidades_api_rest_eliminar')]
    public function eliminar(Request $request, int $id): Response
    {
        $response = $this->client->request(
            'POST',
            'https://www.api.tamila.cl/api/login',
            [
                'json' => [
                    'correo' => 'info@tamila.cl',
                    'password' => 'p2gHNiENUw'
                ]
            ]
        );

        $responseJson = $response->getContent();
        $responseData = json_decode($responseJson, true, 512, JSON_THROW_ON_ERROR);
        $token = $responseData['token'];

        $response = $this->client->request(
            'DELETE',
            'https://www.api.tamila.cl/api/categorias/'.$id,
            [
                'headers' => [
                    'Authorization' => 'Bearer '.$token
                ]
            ]

        );

        $response = $response->getStatusCode();

        if($response === 201){
            $this->addFlash('css','success');
            $this->addFlash('respuesta',$response);
            $this->addFlash('mensaje','proceso completado con éxito');
        }else{
            $this->addFlash('css','danger');
            $this->addFlash('respuesta',$response);
            $this->addFlash('mensaje','vuelve a intentarlo más tarde');
        }

        
       
        return $this->redirectToRoute('utilidades_api_rest_acciones');
        // return $this->render('utilidades/api_rest_acciones.html.twig', compact('response'));
    }

    #[Route('/utilidades/fyleSystem', name: 'utilidades_fileSystem')]
    public function fileSystem(): Response
    {
        $fileSystem = new Filesystem();
        $ejemplo_mkdir = 'C:\xampp\htdocs\composer\fylesystem';
        
        if(!$fileSystem->exists($ejemplo_mkdir)){
            $fileSystem->mkdir($ejemplo_mkdir,0777);

        }else{
            // $fileSystem->copy('C:\Users\Lenovo\OneDrive\Imágenes\2.png', $ejemplo_mkdir.'\foto_copiada.png');
            // $fileSystem->rename($ejemplo_mkdir.'\foto_copiada.png',$ejemplo_mkdir.'\nombre_modificado.png');
            $fileSystem->remove([$ejemplo_mkdir.'\nombre_modificado.png']);
        }
        return $this->render('utilidades/fyle_system.html.twig');
    }

    #[Route('/utilidades/pdf', name: 'utilidades_pdf')]
    public function pdf(): Response
    {
        return $this->render('utilidades/crear_pdf.html.twig');

    }

    #[Route('/utilidades/pdf/generar', name: 'utilidades_pdf_generar')]
    public function pdf_generar(): Response
    {
        // https://github.com/dompdf/dompdf
        $data = [
            'imageSrc' =>$this->imageToBase64($this->getParameter('kernel.project_dir').'/public/img/foto1.jpg'),
            'nombre' => 'Laura Dolores',
            'pais' => 'España',
            'telefono' => '+34 631 99 14 60',
            'correo' => 'prueba@gmail.com'

        ];      
        


        $html = $this -> renderView('utilidades/generar_pdf.html.twig',$data);

        $dompdf = new Dompdf(array('enable_remote' => true));
        $options = $dompdf->getOptions();
        $options->setDefaultFont('Courier');
        $dompdf->setOptions($options);
        

        $dompdf->loadHtml($html);
        $dompdf->render();
        return new Response(
            $dompdf->stream('resume', ['Attachment' => false]),
            Response ::HTTP_OK,
            ['Content-Type' => 'application/pdf'],
            compact('data')
        );
    }

    private function imageToBase64($path){
        $path =$path;
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/'.$type.';base64,'.base64_encode($data);
        return $base64;
    }
}
