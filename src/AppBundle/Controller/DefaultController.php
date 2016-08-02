<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Quizz;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\JsRoutingBundle\FOSJsRoutingBundle;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $quizz = $this->getDoctrine()->getRepository(Quizz::class)->findAll();
        return $this->render(':default:quiz.html.twig', array(
            'questions' => $quizz,
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/new", name="add_quizz")
     */
    public function newAction(Request $request)
    {
        $quizz = new Quizz();
        $form = $this->createFormBuilder($quizz)
            ->add('Question', TextType::class)
            ->add('Reponse', TextType::class)
            ->add('Envoyer', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($quizz);
            $em->flush();
            return $this->redirectToRoute('homepage');
        }
        return $this->render(':default:addquiz.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Quizz $quizz
     * @return JsonResponse
     * @Route("/verif/{id}", name="verification", options={"expose"=true})
     */
    public function verifAction(Quizz $quizz)
    {
        $quizzReponses = $quizz->getReponse();
        if ($quizzReponses == $quizz->getQuestion()) {
            return new JsonResponse("OK", 200);
        }
        return new JsonResponse('Vous n\'avez pas trouvé toutes les réponses');
    }

    public function allGoodAction(Quizz $quizz)
    {

    }
}
