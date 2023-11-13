<?php

namespace App\Controller\Utilisateur;

use App\Entity\UtilisateurSimple;
use App\Form\UtilisateurSimpleType;
use App\Repository\UtilisateurSimpleRepository;
use App\Service\ActionRender;
use App\Service\FormError;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\BaseController;

#[Route('/utilisateur/utilisateur/simple')]
class UtilisateurSimpleController extends BaseController
{
    #[Route('/', name: 'app_utilisateur_utilisateur_simple_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $permission = $this->menu->getPermissionIfDifferentNull($this->security->getUser()->getGroupe()->getId(), self::INDEX_ROOT_NAME);

        $table = $dataTableFactory->create()
            ->add('id', TextColumn::class, ['label' => 'Identifiant'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => UtilisateurSimple::class,
            ])
            ->setName('dt_app_utilisateur_utilisateur_simple');
        if ($permission != null) {

            $renders = [
                'edit' =>  new ActionRender(function () use ($permission) {
                    if ($permission == 'R') {
                        return false;
                    } elseif ($permission == 'RD') {
                        return false;
                    } elseif ($permission == 'RU') {
                        return true;
                    } elseif ($permission == 'RUD') {
                        return true;
                    } elseif ($permission == 'CRU') {
                        return true;
                    } elseif ($permission == 'CR') {
                        return false;
                    } else {
                        return true;
                    }
                }),
                'delete' => new ActionRender(function () use ($permission) {
                    if ($permission == 'R') {
                        return false;
                    } elseif ($permission == 'RD') {
                        return true;
                    } elseif ($permission == 'RU') {
                        return false;
                    } elseif ($permission == 'RUD') {
                        return true;
                    } elseif ($permission == 'CRU') {
                        return false;
                    } elseif ($permission == 'CR') {
                        return false;
                    } else {
                        return true;
                    }
                }),
                'show' => new ActionRender(function () use ($permission) {
                    if ($permission == 'R') {
                        return true;
                    } elseif ($permission == 'RD') {
                        return true;
                    } elseif ($permission == 'RU') {
                        return true;
                    } elseif ($permission == 'RUD') {
                        return true;
                    } elseif ($permission == 'CRU') {
                        return true;
                    } elseif ($permission == 'CR') {
                        return true;
                    } else {
                        return true;
                    }
                    return true;
                }),

            ];


            $hasActions = false;

            foreach ($renders as $_ => $cb) {
                if ($cb->execute()) {
                    $hasActions = true;
                    break;
                }
            }

            if ($hasActions) {
                $table->add('id', TextColumn::class, [
                    'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, UtilisateurSimple $context) use ($renders) {
                        $options = [
                            'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                            'target' => '#exampleModalSizeLg2',

                            'actions' => [
                                'edit' => [
                                    'url' => $this->generateUrl('app_utilisateur_utilisateur_simple_edit', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-pen', 'attrs' => ['class' => 'btn-default'], 'render' => $renders['edit']
                                ],
                                'show' => [
                                    'url' => $this->generateUrl('app_utilisateur_utilisateur_simple_show', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-eye', 'attrs' => ['class' => 'btn-primary'], 'render' => $renders['show']
                                ],
                                'delete' => [
                                    'target' => '#exampleModalSizeNormal',
                                    'url' => $this->generateUrl('app_utilisateur_utilisateur_simple_delete', ['id' => $value]), 'ajax' => true, 'icon' => '%icon% bi bi-trash', 'attrs' => ['class' => 'btn-main'],  'render' => $renders['delete']
                                ]
                            ]

                        ];
                        return $this->renderView('_includes/default_actions.html.twig', compact('options', 'context'));
                    }
                ]);
            }
        }

        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }


        return $this->render('utilisateur/utilisateur_simple/index.html.twig', [
            'datatable' => $table,
            'permition' => $permission
        ]);
    }

    #[Route('/new', name: 'app_utilisateur_utilisateur_simple_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UtilisateurSimpleRepository $utilisateurSimpleRepository, FormError $formError): Response
    {
        $utilisateurSimple = new UtilisateurSimple();
        $form = $this->createForm(UtilisateurSimpleType::class, $utilisateurSimple, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_utilisateur_utilisateur_simple_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_utilisateur_utilisateur_simple_index');


            if ($form->isValid()) {

                $utilisateurSimpleRepository->save($utilisateurSimple, true);
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

        return $this->renderForm('utilisateur/utilisateur_simple/new.html.twig', [
            'utilisateur_simple' => $utilisateurSimple,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_utilisateur_utilisateur_simple_show', methods: ['GET'])]
    public function show(UtilisateurSimple $utilisateurSimple): Response
    {
        return $this->render('utilisateur/utilisateur_simple/show.html.twig', [
            'utilisateur_simple' => $utilisateurSimple,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_utilisateur_utilisateur_simple_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UtilisateurSimple $utilisateurSimple, UtilisateurSimpleRepository $utilisateurSimpleRepository, FormError $formError): Response
    {

        $form = $this->createForm(UtilisateurSimpleType::class, $utilisateurSimple, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_utilisateur_utilisateur_simple_edit', [
                'id' =>  $utilisateurSimple->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_utilisateur_utilisateur_simple_index');


            if ($form->isValid()) {

                $utilisateurSimpleRepository->save($utilisateurSimple, true);
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

        return $this->renderForm('utilisateur/utilisateur_simple/edit.html.twig', [
            'utilisateur_simple' => $utilisateurSimple,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_utilisateur_utilisateur_simple_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, UtilisateurSimple $utilisateurSimple, UtilisateurSimpleRepository $utilisateurSimpleRepository): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_utilisateur_utilisateur_simple_delete',
                    [
                        'id' => $utilisateurSimple->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $utilisateurSimpleRepository->remove($utilisateurSimple, true);

            $redirect = $this->generateUrl('app_utilisateur_utilisateur_simple_index');

            $message = 'Opération effectuée avec succès';

            $response = [
                'statut'   => 1,
                'message'  => $message,
                'redirect' => $redirect,
                'data' => $data
            ];

            $this->addFlash('success', $message);

            if (!$request->isXmlHttpRequest()) {
                return $this->redirect($redirect);
            } else {
                return $this->json($response);
            }
        }

        return $this->renderForm('utilisateur/utilisateur_simple/delete.html.twig', [
            'utilisateur_simple' => $utilisateurSimple,
            'form' => $form,
        ]);
    }
}
