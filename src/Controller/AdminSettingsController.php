<?php

namespace App\Controller;

use App\Entity\SystemSettings;
use App\Form\SystemSettingsType;
use App\Repository\SystemSettingsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin')]
class AdminSettingsController extends AbstractController
{
    #[Route('/settings', name: 'app_admin_settings')]
    public function index(Request $request, SystemSettingsRepository $settingsRepository, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $settings = $settingsRepository->getSettings();
        $form = $this->createForm(SystemSettingsType::class, $settings);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $logoFile = $form->get('logoFile')->getData();

            if ($logoFile) {
                $originalFilename = pathinfo($logoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$logoFile->guessExtension();

                try {
                    $logoFile->move(
                        $this->getParameter('logos_directory'),
                        $newFilename
                    );
                    $settings->setLogoPath($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Failed to upload logo.');
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'System settings updated successfully!');

            return $this->redirectToRoute('app_admin_settings');
        }

        return $this->render('admin_settings/index.html.twig', [
            'form' => $form->createView(),
            'settings' => $settings,
        ]);
    }
}
