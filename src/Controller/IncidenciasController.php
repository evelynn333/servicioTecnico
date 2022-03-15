<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\Entity\Incidencia;
use App\Entity\Cliente;
use App\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class IncidenciasController extends AbstractController {

    /**
     *  @Route("/incidencias", name="app_incidencias")
     *  
     */
    #[Route('/incidencias', name: 'app_incidencias')]
    public function index(ManagerRegistry $doctrine): Response {
        if ($this->getUser() === null) {
            return $this->redirectToRoute("login");
        }
        $repositorio = $doctrine->getRepository(Incidencia::class);
        $incidencias = $repositorio->findBy(
                [],
                ["fechaCreacion" => "DESC"]
        );
        return $this->render('incidencia/verIncidencia.html.twig', [
                    'controller_name' => 'incidencias',
                    'mostarIncidencias' => $incidencias,
        ]);
    }

    /**
     * @Route("/incidencia/insertar/{id<\d+>}", name="insertar_incidencia")
     */
    public function insertarIncidencia(Cliente $cliente, Request $request, ManagerRegistry $doctrine): Response {
        if ($this->getUser() === null) {
            return $this->redirectToRoute("login");
        }
        $incidencia = new Incidencia();
        $form = $this->createFormBuilder($incidencia)
                ->add('Titulo', TextType::class, [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'El titulo es obligatorio',
                                ]),
                    ],
                    'attr' => array(
                        'placeholder' => 'Titulo...',
                    )
                ])
                ->add('Estado', ChoiceType::class, [
                    'choices' => [
                        'Iniciada' => "Iniciada",
                        'En proceso' => "En proceso",
                        'Resuelta' => "Resuelta",
                    ],
                ])
                ->add('Insertar', SubmitType::class)
                ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $titulo = $form->get('Titulo')->getData();
            $estado = $form->get('Estado')->getData();

            $incidencia->setTitulo($titulo);
            $incidencia->setEstado($estado);
            $incidencia->setCliente($cliente);
            $incidencia->setUsuario($this->getUser());
            $incidencia->setFechaCreacion(new \DateTime());
            $em = $doctrine->getManager();
            $em->persist($incidencia);
            $em->flush();
            return $this->redirectToRoute('listado_clientes');
        }
        return $this->renderForm('incidencia/insertaIncidencia.html.twig', ['form_inserta_incidencia' => $form]);
    }

    /**
     * @Route("/incidencia/insertar", name="insertar_incidencia_desde")
     */
    public function insertarIncidenciaDesdeIncidencia(Request $request, ManagerRegistry $doctrine): Response {
        if ($this->getUser() === null) {
            return $this->redirectToRoute("login");
        }
        $incidencia = new Incidencia();
        $form = $this->createFormBuilder($incidencia)
                ->add('Titulo', TextType::class, [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'El titulo es obligatorio',
                                ]),
                    ],
                    'attr' => array(
                        'placeholder' => 'Titulo...',
                    )
                ])
                ->add('Estado', ChoiceType::class, [
                    'choices' => [
                        'Iniciada' => "Iniciada",
                        'En proceso' => "En proceso",
                        'Resuelta' => "Resuelta",
                    ],
                ])
                ->add('Cliente', EntityType::class, [
                    'class' => Cliente::class,
                    'choice_label' => 'nombre',
                    'choice_value' => 'id',
                ])
                ->add('Insertar', SubmitType::class)
                ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $titulo = $form->get('Titulo')->getData();
            $estado = $form->get('Estado')->getData();
            $cliente = $form->get('Cliente')->getData();

            $incidencia->setTitulo($titulo);
            $incidencia->setEstado($estado);
            $incidencia->setCliente($cliente);
            $incidencia->setUsuario($this->getUser());
            $incidencia->setFechaCreacion(new \DateTime());
            $em = $doctrine->getManager();
            $em->persist($incidencia);
            $em->flush();
            return $this->redirectToRoute('app_incidencias');
        }
        return $this->renderForm('incidencia/insertarIncidenciaDesdeIncidencia.html.twig', ['form_inserta_incidencia_desde' => $form]);
    }

    /**
     * @Route("/incidencia/borrar/{id<\d+>}",name="borrar_incidencia")
     */
    public function borrar(Incidencia $incidencia, ManagerRegistry $doctrine): Response {
        if ($this->getUser() === null) {
            return $this->redirectToRoute("login");
        }
        $em = $doctrine->getManager();
        $em->remove($incidencia);
        $em->flush();
       
        return $this->redirectToRoute("listado_clientes");
    }

    /**
     * @Route("/incidencia/editar/{id<\d+>}", name="editar_incidencia")
     */
    public function editar(Incidencia $editaincidencia, Request $request, ManagerRegistry $doctrine): Response {
        if ($this->getUser() === null) {
            return $this->redirectToRoute("login");
        }
        $incidencia = new Incidencia();
        $form = $this->createFormBuilder($editaincidencia)
                ->add('Titulo', TextType::class, [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'El titulo no puede estar vacio',
                                ]),
                    ],
                    'data' => $editaincidencia->getTitulo(),
                    'attr' => array(
                        'placeholder' => 'Titulo...',
                    )
                ])
                ->add('Estado', ChoiceType::class, [
                    'choices' => [
                        $editaincidencia->getEstado() => $editaincidencia->getEstado(),
                        'Iniciada' => "Iniciada",
                        'En proceso' => "En proceso",
                        'Resuelta' => "Resuelta",
                    ],
                ])
                ->add('submit', SubmitType::class, array(
                    'label' => 'Modificar Incidencia',
                ))
                ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $titulo = $form->get('Titulo')->getData();
            $estado = $form->get('Estado')->getData();

            $editaincidencia->setTitulo($titulo);
            $editaincidencia->setEstado($estado);
            $editaincidencia->setCliente($editaincidencia->getCliente());
            $editaincidencia->setUsuario($editaincidencia->getUsuario());
            $editaincidencia->setFechaCreacion($editaincidencia->getFechaCreacion());
            $em = $doctrine->getManager();
            $em->flush();

           
            return $this->redirectToRoute("listado_clientes");
        }
        return $this->renderForm('incidencia/editarFormulario.html.twig', ['form_incidencia' => $form]);
    }

    /**
     * @Route("/incidencia/{id<\d+>}",name="ver_incidencia")
     */
    public function ver(Incidencias $incidencia, Request $request, ManagerRegistry $doctrine): Response {
        if ($this->getUser() === null) {
            return $this->redirectToRoute("login");
        }
        $repositorio = $doctrine->getRepository(Incidencia::class);
        $id = $request->get('id');
        $incidencia = $repositorio->find($id);

        return $this->render('incidencia/verIncidencia.html.twig', [
                    'mostarIncidencias' => $incidencia,
        ]);
    }

}
