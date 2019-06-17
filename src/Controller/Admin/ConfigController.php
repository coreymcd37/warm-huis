<?php

namespace One\CheckJeHuis\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use One\CheckJeHuis\Entity\ConfigTransformation;
use One\CheckJeHuis\Form\ConfigTransformationType;
use One\CheckJeHuis\Repository\ConfigCategoryRepository;
use One\CheckJeHuis\Repository\ConfigRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ConfigController extends Controller
{
    private $configRepository;
    private $entityManager;
    private $configCategoryRepository;

    public function __construct(
        ConfigRepository $configRepository,
        ConfigCategoryRepository $configCategoryRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->configRepository = $configRepository;
        $this->entityManager = $entityManager;
        $this->configCategoryRepository = $configCategoryRepository;
    }

    public function indexAction()
    {
        // ensure there are no duplicate configurations
        $this->configRepository->removeDuplicateTransformations();

        return $this->render(':Config:index.html.twig', array(
            'categories' => $this->configRepository->getAllCategories(),
        ));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function updateCategoryPercentAction(Request $request)
    {
        $data = array(
            'success' => true,
            'errors' => array(),
        );

        try {
            $this->configCategoryRepository->updateCategoryPercentBySlug(
                $request->get('slug'),
                $request->get('percent')
            );
        } catch (EntityNotFoundException $e) {
            error_log($e->getMessage());
            error_log($e->getTraceAsString());

            $data['success'] = false;
            $data['errors'][] = $e->getMessage();
        }

        return new JsonResponse($data);
    }
    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function updateConfigTransformationAction(Request $request, $configFrom, $configTo, $inverse = false)
    {
        $from = $this->configRepository->getConfig($configFrom);
        $to = $this->configRepository->getConfig($configTo);
        $transformation = $from->getTransformationFor($to, $inverse);

        if (!$transformation) {
            $transformation = new ConfigTransformation();
            $transformation
                ->setFromConfig($from)
                ->setToConfig($to)
                ->setInverse($inverse)
                ->setUnit('%');
        }

        $form = $this->createForm(ConfigTransformationType::class, $transformation);
        $success = true;

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $this->configRepository->updateConfigTransformation($transformation);
            } else {
                $success = false;
            }
        }

        $formTemplate = $this->renderView(':Config:update-config-transformation.html.twig', array(
            'form'          => $form->createView(),
            'configFrom'    => $from,
            'configTo'      => $to,
            'inverse'       => $inverse,
        ));

        // return a JSON response with the rendered form HTML as a property
        return new JsonResponse(array(
            'success'   => $success,
            'template'  => $formTemplate
        ));
    }

    public function labelsAction(Request $request)
    {
        $categories = $this->configRepository->getAllCategories();

        if ($request->isMethod('POST')) {
            $data = $request->get('config');
            foreach ($categories as $cat) {
                foreach ($cat->getConfigs() as $config) {
                    if (isset($data[$config->getId()])) {
                        $config->setLabel($data[$config->getId()]);
                        $this->entityManager->persist($config);
                    }
                }
            }

            $this->entityManager->flush();
        }

        return $this->render(':Config:labels.html.twig', array(
            'categories' => $categories,
        ));
    }
}
