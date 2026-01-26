<?php

namespace App\Controller;

use App\Entity\Doctor;
use App\Entity\User;
use App\Form\DoctorType;
use App\Form\RegistrationFormType;
use App\Repository\DoctorRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/doctor')]
class DoctorController extends AbstractController
{
    #[Route('/', name: 'app_doctor_index', methods: ['GET'])]
    public function index(DoctorRepository $doctorRepository): Response
    {
        return $this->render('doctor/index.html.twig', [
            'doctors' => $doctorRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_doctor_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DoctorRepository $doctorRepository, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $doctor = new Doctor();
        
        $userForm = $this->createForm(RegistrationFormType::class, $user);
        $doctorForm = $this->createForm(DoctorType::class, $doctor);

        $userForm->handleRequest($request);
        $doctorForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid() && $doctorForm->isSubmitted() && $doctorForm->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $userForm->get('plainPassword')->getData()
                )
            );
            $user->setRoles(['ROLE_DOCTOR']);
            $userRepository->save($user, true);

            $doctor->setUser($user);
            $doctorRepository->save($doctor, true);

            return $this->redirectToRoute('app_doctor_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('doctor/new.html.twig', [
            'doctor' => $doctor,
            'userForm' => $userForm->createView(),
            'doctorForm' => $doctorForm->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_doctor_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Doctor $doctor): Response
    {
        return $this->render('doctor/show.html.twig', [
            'doctor' => $doctor,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_doctor_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Doctor $doctor, DoctorRepository $doctorRepository): Response
    {
        $form = $this->createForm(DoctorType::class, $doctor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $doctorRepository->save($doctor, true);

            return $this->redirectToRoute('app_doctor_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('doctor/edit.html.twig', [
            'doctor' => $doctor,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_doctor_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Doctor $doctor, DoctorRepository $doctorRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $doctor->getId(), $request->request->get('_token'))) {
            $doctorRepository->remove($doctor, true);
            // Optional: Also delete associated User if desired, but typically we just disable/remove the profile
        }

        return $this->redirectToRoute('app_doctor_index', [], Response::HTTP_SEE_OTHER);
    }
}
