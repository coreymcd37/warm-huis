<?php

namespace One\CheckJeHuis\Service;

use One\CheckJeHuis\Entity\Content;
use One\CheckJeHuis\Entity\House;
use One\CheckJeHuis\Entity\User;
use One\CheckJeHuis\Repository\ContentRepository;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MailService extends AbstractService
{
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var TwigEngine
     */
    protected $twig;

    /**
     * @var FileLocator
     */
    protected $fileLocator;

    /**
     * @var \One\CheckJeHuis\Repository\ContentRepository
     */
    private $contentRepository;

    /** @var \One\CheckJeHuis\Service\HouseService $houseService */
    private $houseService;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var string
     */
    protected $fosUserFromEmailAddress;

    public function __construct(
        \Swift_Mailer $mailer,
        TwigEngine $twig,
        FileLocator $fileLocator,
        ContentRepository $contentRepository,
        HouseService $houseService,
        string $fosUserFromEmailAddress
    ) {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->fileLocator = $fileLocator;
        $this->contentRepository = $contentRepository;
        $this->houseService = $houseService;
        $this->fosUserFromEmailAddress = $fosUserFromEmailAddress;
    }

    protected function getAssetContent($assetURI)
    {
        return base64_encode(file_get_contents($this->getAssetPath(
            $assetURI
        )));
    }

    protected function getAssetPath($assetURI)
    {
        return $this->fileLocator->locate(
            $assetURI
        );
    }

    public function mailHouseToken(House $house)
    {
        $mail = \Swift_Message::newInstance();
        $city = $house->getCity();

        $mail
            ->setContentType('text/html')
            ->setSubject('Mijn warm huis')
            ->addFrom($city->getEmail())
            ->setTo($house->getEmail())
            ->setBody(
                $this->twig->render(':Email:save-token.html.twig', array(
                    'house'                 => $house,
                ))
            )
        ;
        $this->mailer->send($mail);
        $this->houseService->anonymizeData($house);
    }

    /**
     * @param House $house
     * @param string $pdf binary string
     */
    public function mailCalculatorPdf(House $house, $pdf)
    {
        $mail = \Swift_Message::newInstance();
        $city = $house->getCity();
        $mail
            ->attach(new \Swift_Attachment($pdf, 'mijn-warm-huis.pdf', 'application/pdf'))
            ->setSubject('Mijn warm huis: mijn stappenplan')
            ->addFrom($city->getEmail())
            ->setTo($house->getEmail())
            ->setBody(
                $this->twig->render(':Email:plan-pdf.html.twig', array(
                    'house' => $house,
                    'content' => $this->contentRepository->getContentBySlug(Content::MAIL_PDF, $city),
                ))
            )
            ->setContentType('text/html')
        ;

        $this->mailer->send($mail);
        $this->houseService->anonymizeData($house);
    }

    public function mailSetPassword(User $user)
    {
        $mail = \Swift_Message::newInstance();

        $mail
            ->setContentType('text/html')
            ->setSubject('Nieuw account')
            ->addFrom($this->fosUserFromEmailAddress)
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render(':Email:new-user.html.twig', array(
                    'user' => $user
                ))
            )
        ;
        $this->mailer->send($mail);
    }
}
