<?php

namespace One\CheckJeHuis\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use One\CheckJeHuis\Controller\AbstractController;
use One\CheckJeHuis\Entity\Content;
use One\CheckJeHuis\Form\ContentType;
use One\CheckJeHuis\Repository\ContentRepository;
use Symfony\Component\HttpFoundation\Request;

class ContentController extends AbstractController
{
    private $contentRepository;
    private $entityManager;

    public function __construct(ContentRepository $contentRepository, EntityManagerInterface $entityManager)
    {
        $this->contentRepository = $contentRepository;
        $this->entityManager = $entityManager;
    }

    public function indexAction($type)
    {
        $city = null;
        if ($type === Content::TYPE_SPECIFIC) {
            $city = $this->getUser()->getCity();
        }

        $contents = $this->contentRepository->getAllContent($city);

        return $this->render(':Content:index.html.twig', array(
            'contents' => $contents,
            'type' => $type,
        ));
    }

    public function editAction(Request $request, Content $content)
    {
        /** @var Content $content */
        $type = $content->getCity() ? 'specific' : 'generic';

        if (!$content->canEdit($this->getUser())) {
            return $this->redirect($this->generateUrl('admin_content', ['type' => $type]));
        }
        $form = $this->createForm(new ContentType($content->canDeactivate()), $content);

        if ($request->isMethod('post')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $this->contentRepository->add($form->getData());


                return $this->redirect($this->generateUrl('admin_content', ['type' => $type]));
            }
        }

        $this->entityManager->flush();

        return $this->render(':Content:edit.html.twig', array(
            'form' => $form->createView(),
            'content' => $content,
        ));
    }

    public function viewAction(Content $content)
    {
        return $this->render(':Content:view.html.twig', array(
            'content' => $content,
        ));
    }
}
