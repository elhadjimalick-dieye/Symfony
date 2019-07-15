<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Employer;
use App\Entity\Service;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/* use App\Entity\Service;
 */

class MalickController extends AbstractController
{
    /**
     * @Route("/malick", name="malick")
     */
    public function index()
    {
        return $this->render('malick/index.html.twig', [
            'controller_name' => 'MalickController',
        ]);
    }




    // creation du formulaire employer


    /**
     * @Route("/", name="home")
     * @Route("/malick/{id}/edit",name="edit_employer")
     */
    public function create(Employer $employer = null, Request $request, ObjectManager $manager)
    {
        if (!$employer) {
            $employer = new Employer();

        }

        $form = $this->createFormBuilder($employer)
            ->add('matricule', TextType::class)
            ->add('nomcomplet', TextType::class)
            ->add('datedenaissance', DateType::class, ['widget' => 'single_text', 'format' => 'yyyy-MM-dd'])
            ->add('salaire', MoneyType::class)
            ->add('idservice', EntityType::class, ['class' => Service::class, 'choice_label' => 'libelle'])
            ->add('save', SubmitType::class, ['label' => 'Ajouter'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isvalid()) {
            $manager->persist($employer);
            $manager->flush();
            $this->addFlash('success','employé ajouté avec succés');
            return $this->redirectToRoute('home');
        }
        dump($employer);

        $this->addFlash('edit', 'employé modifié avec succés');
        return $this->render('malick/home.html.twig', [
            'formArticle' => $form->createView(), 'editmode' => $employer->getId() !== null
        ]);
    }

    // création du formulaire service

    /**
     * @Route("/malick/service", name="service")
     */
    public function creates(Request $requeste, ObjectManager $manager)
    {
        $service = new Service();

        $form = $this->createFormBuilder($service)
            ->add('libelle', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Ajouter service'])
            ->getForm();

        $form->handleRequest($requeste);

        if ($form->isSubmitted() && $form->isvalid()) {
            $manager->persist($service);
            $manager->flush();
            return $this->redirectToRoute('service');
        }

        dump($service);


        return $this->render('malick/service.html.twig', [
            'formService' => $form->createView()
        ]);
    }

    /**
     * @Route("/malick/list", name="liste")
     */
    public function list()
    {
        $find = $this->getDoctrine()->getRepository(Employer::class);
        $employer = $find->findAll();

        return $this->render('malick/list.html.twig', [
            'controller' => 'EmployerController',
            'employers' => $employer
        ]);
        
    }

    /**
     * @Route("/malick/servicess", name="servic")
     */
    public function liste()
    {
        $find = $this->getDoctrine()->getRepository(Service::class);
        $servicess = $find->findAll();

        return $this->render('malick/service.html.twig', [
            'controller' => 'ServiceController',
            'services' => $servicess
        ]);
    }


    /**
     * @Route("/malick/{id}/supemp", name="listes")
     */
    public function sup(Employer $employer, ObjectManager $manager)
    {
     
        $del = $this->getDoctrine()->getRepository(Employer::class);
        $employe = $del->findAll();
        $manager->remove($employer);
        $manager->flush();
        $this->addFlash('danger', 'employé supprimé avec succés');

        return $this->redirectToRoute('liste', ['employers' => $employe]);
    }
}
