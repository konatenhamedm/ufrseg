<?php

namespace App\Controller\Site;

use App\Controller\BaseController;
use App\Entity\Employe;
use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\EmployeRepository;
use App\Repository\UtilisateurRepository;
use App\Service\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class HomeController extends BaseController
{

    #[Route(path: '/', name: 'site_home')]
    public function index(Request $request): Response
    {
        return $this->render('site/home/index.html.twig');
    }

    #[Route(path: '/edition', name: 'site_edition')]
    public function edition(Request $request): Response
    {
        return $this->render('site/pages/edition.html.twig');
    }
    #[Route(path: '/presentation', name: 'site_presentation')]
    public function presentation(Request $request, UtilisateurRepository $utilisateurRepository, FormError $formError): Response
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_utilisateur_utilisateur_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_utilisateur_utilisateur_index');


            //dd($form->getData()->getPassword());

            if ($form->isValid()) {

                //$utilisateur->setPassword($this->hasher->hashPassword($utilisateur, $form->getData()->getPassword()));
                $utilisateurRepository->add($utilisateur, true);
                $data = true;
                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = Response::HTTP_INTERNAL_SERVER_ERROR;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }


            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->renderForm('site/pages/presentation.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);

        //return $this->render('site/pages/presentation.html.twig');
    }
    #[Route(path: '/mission', name: 'site_mission')]
    public function mission(Request $request): Response
    {
        return $this->render('site/pages/mission.html.twig');
    }
    #[Route(path: '/site/admin', name: 'site_admin')]
    public function admin(Request $request): Response
    {
        return $this->render('site/admin/index.html.twig');
    }
    #[Route(path: '/equipe', name: 'site_equipe')]
    public function equipe(Request $request): Response
    {
        return $this->render('site/pages/equipe.html.twig');
    }




    #[Route('/inscription', name: 'site_inscription', methods: ['GET', 'POST'])]
    public function inscription(Request $request, UtilisateurRepository $utilisateurRepository,EmployeRepository $employeRepository, FormError $formError,MailerInterface $mailer): Response
    {
        /*$dsn = $this->getParameter('mail_dsn');
        $transport = Transport::fromDsn($dsn);
        $mailer = new Mailer($transport);*/
        //$ccs = [$this->getUser()->getEmploye()->getAdresseMail()];


        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur, [
            'method' => 'POST',
            'action' => $this->generateUrl('site_inscription')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('site_home');


//dd($form->getData()->getPassword());

          /*  $email = (new Email())
                ->from(new Address('konatefvaly@gmail.com', 'Fabien'))
                ->to(new Address($form->get('email')->getData(),"eeee"))
                ->subject('DEMANDE DE COTATIONS')
                ->html(<<<HTML
    Bonjour la société<br><br>,
    Veuillez trouver en pièce jointe une demande de cotation<br><br>
    Bonne réception
    
    HTML);*/

            if ($form->isValid()) {



                $employe = new Employe();
                $employe->setContact('788877');
                $employe->setNom($form->get('nom')->getData());
                $employe->setPrenom($form->get('prenoms')->getData());
                $employe->setAdresseMail($form->get('email')->getData());
                $employe->setMatricule("ff");
                $employe->setDateNaissance($form->get('dateNaissance')->getData());
                $employeRepository->add($employe,true);

                $utilisateur->setEmploye($employe);
                $utilisateur->setPassword($this->hasher->hashPassword($utilisateur, $form->get('password')->getData()));
                $utilisateurRepository->add($utilisateur, true);
                $data = true;
                $message       = 'Opération effectuée avec succès,vous pouvez verifier votre compte gmail';
                $statut = 1;
                $this->addFlash('success', $message);
                //$mailer->send($email);

            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = Response::HTTP_INTERNAL_SERVER_ERROR;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }

            }


            if ($isAjax) {
                return $this->json( compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }


        }

        return $this->renderForm('site/pages/inscription.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }

    #[Route(path: '/candidat', name: 'site_candidat')]
    public function candidat(Request $request): Response
    {
        return $this->render('site/pages/candidats.html.twig');
    }
    #[Route(path: '/reglement_presentation', name: 'site_reglement_presentation')]
    public function reglement(Request $request): Response
    {
        return $this->render('site/pages/reglement_presentation.html.twig');
    }
}
