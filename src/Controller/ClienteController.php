<?php

namespace App\Controller;

use App\Entity\Incidencia;
use App\Entity\Cliente;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use \Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Validator\Constraints\NotBlank;

class ClienteController extends AbstractController {

    /**
     *  @Route("/cliente", name="listado_clientes")
     *  
     */
    #[Route('/cliente', name: 'listado_clientes')]
    public function index(ManagerRegistry $doctrine): Response {
        if ($this->getUser() === null) {
            return $this->redirectToRoute("login");
        }
        $repositorio = $doctrine->getRepository(Cliente::class);
        $cliente = $repositorio->findAll();

        return $this->render('cliente/index.html.twig', [
                    'clientes' => $cliente,
           
        ]);
    }

    /**
     * 
     * @Route("/cliente/borrar/{id<\d+>}", name="borrar_cliente")
     */
    public function borrar(Cliente $cliente, ManagerRegistry $doctrine): Response {
        if ($this->getUser() === null) {
            return $this->redirectToRoute("login");
        }
        $em = $doctrine->getManager();
        $em->remove($cliente);
        $em->flush();

        $this->addFlash("aviso", "Cliente borrado");
        return $this->redirectToRoute("listado_clientes");
    }

    /**
     * Inserta un  cliente utilizando los formularios de symfony
     * @Route("/cliente/insertarCliente", name="insertar_cliente")
     */
    public function insertarCliente(Request $request, ManagerRegistry $doctrine): Response {
        if ($this->getUser() === null) {
            return $this->redirectToRoute("login");
        }
        $cliente = new Cliente();
        $form = $this->createFormBuilder($cliente)
                ->add('nombre', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'El nombre es obligatorio',
                    ]),
                ],
                'attr' => array(
                    'placeholder' => 'Nombre...',
                   
                )
            ])
               ->add('apellidos',TextType::class, [
                    'label' => false,
                    'attr' => array(
                    'placeholder' => 'Apellidos... ',
                   
                ),
                ])
                ->add('telefono', NumberType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'El telefono es obligatorio',
                    ]),
                ],
                'attr' => array(
                    'placeholder' => 'Telefono...',
                   
                )
            ])
                ->add('direccion', TextType::class, [
                    'label' => false,
                    'attr' => array(
                    'placeholder' => 'Direccion... ',
                   
                ),
                ])
                ->add('Insertar', SubmitType::class)
                ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cliente = $form->getData();
            $em = $doctrine->getManager();
            $em->persist($cliente);
            $em->flush();
            return $this->redirectToRoute('listado_clientes');
        }
        return $this->renderForm('cliente/insertarCliente.html.twig', ['form_cliente' => $form]);
    }

    /**
     * @Route("/cliente/{id}",name="ver_cliente")
     */
   public function ver(Cliente $cliente, Request $request, ManagerRegistry $doctrine):Response {
        if ($this->getUser() === null) {
            return $this->redirectToRoute("login");
        }
        $incidencias = new Incidencia(); 
        $repositorio = $doctrine->getRepository(Cliente::class);
        $repositorio2 = $doctrine->getRepository(Incidencia::class);
        $id = $request->get('id');
        $cliente = $repositorio->find($id);
        $incidencias = $repositorio2->findByIdCliente($id);
        return $this->render("cliente/ver.html.twig", [
            "cliente" => $cliente,
            "incidenciasCliente"=>$incidencias]);

}

   }
