<?php

namespace One\CheckJeHuis\Controller\Admin;

use FOS\UserBundle\Model\UserManager;
use One\CheckJeHuis\Entity\User;
use One\CheckJeHuis\Form\UserType;
use One\CheckJeHuis\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route(service="one.check_je_huis.controller.admin.user_controller")
 */
class UserController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserManager
     */
    private $userManager;

    public function __construct(UserRepository $userRepository, UserManager $userManager)
    {
        $this->userRepository = $userRepository;
        $this->userManager = $userManager;
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function listAction()
    {
        $users = $this->userRepository->findAll();

        return $this->render(':Admin/User:list.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function addAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $tokenGenerator = $this->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
            $user->setPasswordRequestedAt(new \DateTime());

            $password = substr($tokenGenerator->generateToken(), 0, 8);
            $user->setPlainPassword($password);

            $this->userManager->updateUser($user, true);
            $request->getSession()->getFlashBag()->add('info', 'Gebruiker toegevoegd');

            $mailer = $this->get('one.check_je_huis.service.mailer');
            $mailer->mailSetPassword($user);

            return new RedirectResponse($this->generateUrl('admin_user_list'));
        }

        return $this->render(':Admin/User:add.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editAction(User $user, Request $request)
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userManager->updateUser($user, true);
            $request->getSession()->getFlashBag()->add('info', 'Gebruiker aangepast');

            return new RedirectResponse($this->generateUrl('admin_user_list'));
        }

        return $this->render(':Admin/User:edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request, User $user)
    {
        $response = array(
            'html' => '',
        );
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_user_delete', ['user' => $user->getId()]))
            ->setMethod('DELETE')
            ->getForm();
        if ($request->getMethod() === 'DELETE') {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->userRepository->remove($user);
            }
            // In case you want to redirect.
            $request->getSession()->getFlashBag()->add('info', 'Gebruiker verwijderd.');

            return new RedirectResponse($this->generateUrl('admin_user_list'));
        }
        $render = $this->render(':Admin/User:delete_confirm.html.twig', array(
            'delete_form' => $form->createView(),
            'user' => $user,
        ));

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }
}